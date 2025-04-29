<?php

class AppExpert_Password_Reset_API
{

    public $api_namespace = 'app-expert/v1';
    public $route_send_otp = '/send-otp';
    public $route_verify_otp = '/verify-otp';
    public $route_reset_password = '/reset-password';

    public $otp_expiration = 300;
    public $otp_length = 6;
    public $max_attempts = 3;

    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes()
    {
        $this->register_route($this->route_send_otp, 'send_otp');
        $this->register_route($this->route_verify_otp, 'verify_otp');
        $this->register_route($this->route_reset_password, 'reset_password');
    }

    private function register_route($route, $callback)
    {
        register_rest_route($this->api_namespace, $route, [
            'methods'  => 'POST',
            'callback' => [$this, $callback],
            'permission_callback' => '__return_true',
        ]);
    }

    public function send_otp(WP_REST_Request $request)
    {
        $email = sanitize_email($request->get_param('email'));

        if (!email_exists($email)) return new WP_Error('email_not_found', 'Email not found.', ['status' => 404]);

        $user = get_user_by('email', $email);
        $meta = AppExpert_Helper::get_user_meta($user->ID);

        $attempts = $meta['otp_attempts'] ?? 0;
        $last_attempt_time = $meta['otp_last_attempt'] ?? 0;

        if ($attempts >= $this->max_attempts && (time() - $last_attempt_time) < 300) {
            $wait_time = ceil((300 - (time() - $last_attempt_time)) / 60);
            return new WP_Error('max_attempts_reached', "Maximum OTP attempts reached. Please wait {$wait_time} minutes before trying again.", ['status' => 429]);
        }

        if ((time() - $last_attempt_time) >= 300) $attempts = 0;

        if ((time() - $last_attempt_time) < 6) {
            return new WP_Error('too_many_requests', 'Please wait 6 seconds before requesting another OTP.', ['status' => 429]);
        }

        $otp = wp_rand(pow(10, $this->otp_length - 1), pow(10, $this->otp_length) - 1);

        AppExpert_Helper::update_user_meta($user->ID, [
            'otp' => $otp,
            'otp_expires' => time() + $this->otp_expiration,
            'otp_attempts' => $attempts + 1,
            'otp_last_attempt' => time(),
        ]);

        EmailManager::send_password_reset($email, $user->display_name, $otp);

        return ['message' => 'OTP sent successfully.', 'otp' => $otp, 'expires_in_min' => $this->otp_expiration / 60];
    }

    public function verify_otp(WP_REST_Request $request)
    {
        $email = sanitize_email($request->get_param('email'));
        $otp = sanitize_text_field($request->get_param('otp'));

        if (!email_exists($email)) return new WP_Error('email_not_found', 'Email not found.', ['status' => 404]);

        $user = get_user_by('email', $email);
        $meta = AppExpert_Helper::get_user_meta($user->ID);

        $saved_otp = $meta['otp'] ?? null;
        $expires = $meta['otp_expires'] ?? null;

        if (!$saved_otp || !$expires || time() > $expires) return new WP_Error('otp_expired', 'OTP has expired.', ['status' => 400]);

        if ($saved_otp != $otp) return new WP_Error('otp_invalid', 'Invalid OTP.', ['status' => 400]);

        AppExpert_Helper::update_user_meta($user->ID, 'otp_verified', true);

        return ['message' => 'OTP verified successfully.'];
    }

    public function reset_password(WP_REST_Request $request)
    {
        $email = sanitize_email($request->get_param('email'));
        $password = sanitize_text_field($request->get_param('password'));
        $confirm_password = sanitize_text_field($request->get_param('confirm_password'));

        if (!email_exists($email)) return new WP_Error('email_not_found', 'Email not found.', ['status' => 404]);

        if ($password !== $confirm_password) return new WP_Error('password_mismatch', 'Passwords do not match.', ['status' => 400]);

        $user = get_user_by('email', $email);
        $meta = AppExpert_Helper::get_user_meta($user->ID);

        $is_verified = $meta['otp_verified'] ?? false;

        if (!$is_verified) return new WP_Error('otp_not_verified', 'OTP not verified.', ['status' => 400]);

        wp_set_password($password, $user->ID);

        AppExpert_Helper::delete_user_meta($user->ID);

        return ['message' => 'Password reset successfully.'];
    }
}

new AppExpert_Password_Reset_API();

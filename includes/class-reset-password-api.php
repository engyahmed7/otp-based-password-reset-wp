<?php

class AppExpert_Password_Reset_API
{
    public function __construct()
    {
        add_action('rest_api_init', [$this, 'register_routes']);
    }

    public function register_routes()
    {
        $this->register_route(ROUTE_SEND_OTP, 'send_otp');
        $this->register_route(ROUTE_VERIFY_OTP, 'verify_otp');
        $this->register_route(ROUTE_RESET_PASSWORD, 'reset_password');
    }

    private function register_route($route, $callback)
    {
        register_rest_route(API_NAMESPACE, $route, [
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
        $otp = wp_rand(100000, 999999);

        update_user_meta($user->ID, 'appExpert_otp', $otp);
        update_user_meta($user->ID, 'appExpert_otp_expires', time() + 10 * 60);

        wp_mail($email, 'Your OTP Code', 'Your OTP is: ' . $otp);

        return ['message' => 'OTP sent successfully.'];
    }

    public function verify_otp(WP_REST_Request $request)
    {
        $email = sanitize_email($request->get_param('email'));
        $otp = sanitize_text_field($request->get_param('otp'));

        if (!email_exists($email)) return new WP_Error('email_not_found', 'Email not found.', ['status' => 404]);


        $user = get_user_by('email', $email);
        $saved_otp = get_user_meta($user->ID, 'appExpert_otp', true);
        $expires = get_user_meta($user->ID, 'appExpert_otp_expires', true);

        if (!$saved_otp || !$expires || time() > $expires) return new WP_Error('otp_expired', 'OTP has expired.', ['status' => 400]);


        if ($saved_otp != $otp) return new WP_Error('otp_invalid', 'Invalid OTP.', ['status' => 400]);


        update_user_meta($user->ID, 'appExpert_otp_verified', true);

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
        $is_verified = get_user_meta($user->ID, 'appExpert_otp_verified', true);

        if (!$is_verified) return new WP_Error('otp_not_verified', 'OTP not verified.', ['status' => 400]);


        wp_set_password($password, $user->ID);

        AppExpert_Helper::clean_otp_meta($user->ID);

        return ['message' => 'Password reset successfully.'];
    }
}

new AppExpert_Password_Reset_API();

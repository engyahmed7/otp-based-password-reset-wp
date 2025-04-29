<?php

class EmailManager
{
    public static function render_template($template_name, $data = [])
    {
        $template_path = PLUGIN_DIR_PATH. 'email-templates/' . $template_name . '.php';

        if (!file_exists($template_path)) return '';

        extract($data);
        ob_start();
        include $template_path;
        return ob_get_clean();
    }

    public static function send_password_reset($to, $user_name, $otp)
    {

        error_log('Sending password reset email to: ' . $user_name);

        $subject = 'Password Reset Request - ' . APP_NAME;
        $headers = ['Content-Type: text/html; charset=UTF-8'];

        $message = self::render_template('password-reset-email', [
            'user_name' => $user_name,
            'otp'       => $otp,
        ]);

        wp_mail($to, $subject, $message, $headers);
    }
}


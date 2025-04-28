<?php

class AppExpert_Helper
{

    public static function clean_otp_meta($user_id)
    {
        delete_user_meta($user_id, 'appExpert_otp');
        delete_user_meta($user_id, 'appExpert_otp_expires');
        delete_user_meta($user_id, 'appExpert_otp_verified');
        delete_user_meta($user_id, 'appExpert_otp_attempts');
        delete_user_meta($user_id, 'appExpert_otp_last_attempt');
    }
}

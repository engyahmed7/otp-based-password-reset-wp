<?php

class AppExpert_Helper
{

    public static function get_user_meta($user_id)
    {
        $meta = get_user_meta($user_id, 'appExpert_meta', true);
        return is_array($meta) ? $meta : (is_string($meta) ? maybe_unserialize($meta) : []);
    }

    public static function update_user_meta($user_id, $data)
    {
        $meta = self::get_user_meta($user_id);
        $meta = is_array($meta) ? $meta : [];

        foreach ($data as $key => $value) {
            $meta[$key] = $value;
        }

        update_user_meta($user_id, 'appExpert_meta', maybe_serialize($meta));
    }

    public static function delete_user_meta($user_id, $key = null)
    {
        if (!$user_id || !is_numeric($user_id)) {
            return new WP_Error('invalid_user_id', 'Invalid user ID provided.', ['status' => 400]);
        }

        if ($key === null) {
            delete_user_meta($user_id, 'appExpert_meta');
        } else {
            $meta = self::get_user_meta($user_id);
            if (isset($meta[$key])) {
                unset($meta[$key]);
                update_user_meta($user_id, 'appExpert_meta', maybe_serialize($meta));
            }
        }
    }
}

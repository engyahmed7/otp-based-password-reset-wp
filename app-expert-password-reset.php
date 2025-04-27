<?php

/**
 * Plugin Name: App Expert Password Reset
 * Description: Custom APIs to reset user password via OTP.
 * Version: 1.0.0
 * Author: AppExperts
 */

if (! defined('ABSPATH')) exit;

class AppExpertPasswordReset
{

    public function __construct()
    {
        $this->load_dependencies();
        $this->initialize_hooks();
    }

    private function load_dependencies()
    {
        require_once('config.php');
        require_once PLUGIN_DIR_PATH . 'includes/index.php';
    }

    private function initialize_hooks()
    {
        new AppExpert_Password_Reset_API();
    }
}

new AppExpertPasswordReset();

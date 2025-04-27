<?php

define('API_NAMESPACE', 'app-expert/v1');
define('ROUTE_SEND_OTP', '/send-otp');
define('ROUTE_VERIFY_OTP', '/verify-otp');
define('ROUTE_RESET_PASSWORD', '/reset-password');

define('OTP_EXPIRATION', 600);  
define('OTP_LENGTH', 6);       
define('MAX_ATTEMPTS', 3); 


define('PLUGIN_DIR_PATH', plugin_dir_path(__FILE__));


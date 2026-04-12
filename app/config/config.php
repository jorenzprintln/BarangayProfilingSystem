<?php

// Appliccation Settings
const APP_NAME = "Barangay Profile System";
const APP_URL = 'http://localhost/barangay_system';
const SINGLE_ADMIN_MODE = true;

// Debug mode (set to false in production)
const DEBUG = false;

// Path Definitions
define('ROOT_PATH', dirname(__DIR__));
define('CONTROLLER_PATH', ROOT_PATH . '/Controllers/');
define('MODEL_PATH', ROOT_PATH . '/Models/');
define('VIEW_PATH', ROOT_PATH . '/Views/');
define('ASSETS_PATH', ROOT_PATH . '/public/assets/');


// Error Reporting
if (DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}
const MAIL_USERNAME = 'jorenzlnu@gmail.com';
const MAIL_PASSWORD = 'mxxj hiul qtbs dwtp';
const MAIL_FROM_NAME = 'Barangay 36-A';
const ADMIN_RECOVERY_KEY = 'Brgy36A@ProfilingSystem';
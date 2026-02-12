<?php

// Appliccation Settings
const APP_NAME = "Barangay Profile System";
const APP_URL = "http://localhost/bps";

// Debug mode (set to false in production)
const DEBUG = true;

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

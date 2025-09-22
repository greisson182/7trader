<?php
declare(strict_types=1);

// Basic constants
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

define('ROOT', dirname(__DIR__));
define('APP', ROOT . DS . 'src' . DS);
define('APP_DIR', 'src');
define('WEBROOT_DIR', 'webroot');
define('WWW_ROOT', ROOT . DS . WEBROOT_DIR . DS);
define('TMP', ROOT . DS . 'tmp' . DS);
define('CONFIG', ROOT . DS . 'config' . DS);
define('CACHE', TMP . 'cache' . DS);
define('LOGS', TMP . 'logs' . DS);

// Create directories if they don't exist
if (!is_dir(TMP)) {
    mkdir(TMP, 0755, true);
}
if (!is_dir(CACHE)) {
    mkdir(CACHE, 0755, true);
}
if (!is_dir(LOGS)) {
    mkdir(LOGS, 0755, true);
}

// Load our minimal autoloader
require ROOT . DS . 'vendor' . DS . 'autoload.php';

// Basic error reporting
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Set timezone
date_default_timezone_set('UTC');

// Basic security salt
if (!defined('SECURITY_SALT')) {
    define('SECURITY_SALT', 'DYhG93b0qyJfIxfs2guVoUubWwvniR2G0FgaC9mi');
}

// Load configuration
$config = [];
if (file_exists(CONFIG . 'app.php')) {
    $config = require CONFIG . 'app.php';
}
if (file_exists(CONFIG . 'app_local.php')) {
    $localConfig = require CONFIG . 'app_local.php';
    $config = array_merge_recursive($config, $localConfig);
}

// Store config globally
$GLOBALS['app_config'] = $config;
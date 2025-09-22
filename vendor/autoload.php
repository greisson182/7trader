<?php

// Minimal autoloader for CakePHP application
spl_autoload_register(function ($class) {
    // Convert namespace to file path
    $file = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    
    // Check in src directory
    $srcFile = __DIR__ . '/../src/' . str_replace('App\\', '', $file);
    if (file_exists($srcFile)) {
        require_once $srcFile;
        return;
    }
    
    // Check for CakePHP core classes (basic implementation)
    $cakeFile = __DIR__ . '/cakephp/cakephp/src/' . str_replace('Cake\\', '', $file);
    if (file_exists($cakeFile)) {
        require_once $cakeFile;
        return;
    }
});

// Define basic constants if not already defined
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__));
}

if (!defined('APP_DIR')) {
    define('APP_DIR', 'src');
}

if (!defined('APP')) {
    define('APP', ROOT . DS . APP_DIR . DS);
}

if (!defined('CONFIG')) {
    define('CONFIG', ROOT . DS . 'config' . DS);
}

if (!defined('WWW_ROOT')) {
    define('WWW_ROOT', ROOT . DS . 'webroot' . DS);
}

if (!defined('TMP')) {
    define('TMP', ROOT . DS . 'tmp' . DS);
}

if (!defined('LOGS')) {
    define('LOGS', ROOT . DS . 'logs' . DS);
}

if (!defined('CACHE')) {
    define('CACHE', TMP . 'cache' . DS);
}

if (!defined('CAKE_CORE_INCLUDE_PATH')) {
    define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'vendor' . DS . 'cakephp' . DS . 'cakephp');
}

if (!defined('CORE_PATH')) {
    define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
}

if (!defined('CAKE')) {
    define('CAKE', CORE_PATH . 'src' . DS);
}

// Basic function implementations
if (!function_exists('env')) {
    function env($key, $default = null) {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}

if (!function_exists('h')) {
    function h($text, $double = true, $charset = null) {
        if ($text === null) {
            return '';
        }
        return htmlspecialchars($text, $double ? ENT_QUOTES | ENT_SUBSTITUTE : ENT_NOQUOTES, $charset ?: 'UTF-8');
    }
}

if (!function_exists('__')) {
    function __($singular, ...$args) {
        return vsprintf($singular, $args);
    }
}
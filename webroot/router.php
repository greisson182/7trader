<?php
// Router for PHP built-in server
$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);

// Remove query string from path
$path = strtok($path, '?');

// Special handling for admin routes - always route through index.php
if (strpos($path, '/admin') === 0 && $path !== '/admin/css' && $path !== '/admin/js' && $path !== '/admin/images') {
    $_SERVER['REQUEST_URI'] = $uri;
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    require_once __DIR__ . '/index.php';
    return;
}

// If it's a real file, serve it
if ($path !== '/' && file_exists(__DIR__ . $path)) {
    return false; // serve the requested resource as-is
}

// Otherwise, route through index.php
$_SERVER['REQUEST_URI'] = $uri;
$_SERVER['SCRIPT_NAME'] = '/index.php';
require_once __DIR__ . '/index.php';
?>
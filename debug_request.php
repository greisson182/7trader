<?php
echo "=== DEBUG REQUEST ===\n";
echo "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n";
echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
echo "PATH_INFO: " . ($_SERVER['PATH_INFO'] ?? 'not set') . "\n";
echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";

$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = trim($path, '/');

echo "Parsed path: '$path'\n";

$segments = explode('/', $path);
echo "Segments: " . print_r($segments, true) . "\n";

// Handle special auth routes (both GET and POST)
if ($path === 'login') {
    $controller = 'Auth';
    $action = 'loginAction';
    $id = null;
    echo "Matched login route\n";
} elseif ($path === 'logout') {
    $controller = 'Auth';
    $action = 'logoutAction';
    $id = null;
    echo "Matched logout route\n";
} else {
    // Default routing logic
    $controller = !empty($segments[0]) ? ucfirst($segments[0]) : 'Students';
    $action = !empty($segments[1]) ? $segments[1] : 'index';
    $id = !empty($segments[2]) ? $segments[2] : null;
    echo "Using default routing\n";
}

echo "Controller: $controller\n";
echo "Action: $action\n";
echo "ID: " . ($id ?? 'null') . "\n";

$controllerFile = dirname(__DIR__) . "/src/Controller/{$controller}Controller.php";
echo "Controller file: $controllerFile\n";
echo "File exists: " . (file_exists($controllerFile) ? 'YES' : 'NO') . "\n";

if (file_exists($controllerFile)) {
    require_once $controllerFile;
    $controllerClass = "App\\Controller\\{$controller}Controller";
    echo "Controller class: $controllerClass\n";
    echo "Class exists: " . (class_exists($controllerClass) ? 'YES' : 'NO') . "\n";
    
    if (class_exists($controllerClass)) {
        $controllerInstance = new $controllerClass();
        echo "Method exists: " . (method_exists($controllerInstance, $action) ? 'YES' : 'NO') . "\n";
    }
}

echo "\n=== POST DATA ===\n";
print_r($_POST);

echo "\n=== SESSION ===\n";
session_start();
print_r($_SESSION);
?>
<?php
/**
 * Market Replay Tracker - Simple PHP Application
 */

// Debug mode - uncomment to debug requests
// include dirname(__DIR__) . '/debug_request.php'; exit;

// Bootstrap the application
require dirname(__DIR__) . '/config/bootstrap.php';

// Simple routing
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = trim($path, '/');

// Remove query parameters for routing
$segments = explode('/', $path);

// Handle special auth routes (both GET and POST)
if ($path === 'login') {
    $controller = 'Auth';
    $action = 'loginAction';
    $id = null;
} elseif ($path === 'logout') {
    $controller = 'Auth';
    $action = 'logoutAction';
    $id = null;
} elseif ($path === 'profile/edit') {
    $controller = 'Profile';
    $action = 'edit';
    $id = null;
} else {
    // Default routing logic
    $controller = !empty($segments[0]) ? ucfirst($segments[0]) : 'Students';
    $action = !empty($segments[1]) ? $segments[1] : 'index';
    $id = !empty($segments[2]) ? $segments[2] : null;
}

// Database connection
function getDbConnection() {
    global $app_config;
    
    // Default database configuration
    $host = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'backtest_db';
    
    // Try to get config from app_config if available
    if (isset($app_config['Datasources']['default'])) {
        $config = $app_config['Datasources']['default'];
        
        // Ensure we get string values, not arrays
        $host = is_string($config['host']) ? $config['host'] : $host;
        $username = is_string($config['username']) ? $config['username'] : $username;
        $password = is_string($config['password']) ? $config['password'] : $password;
        $database = is_string($config['database']) ? $config['database'] : $database;
    }
    
    try {
        $dsn = "mysql:host={$host};dbname={$database};charset=utf8";
        $pdo = new PDO($dsn, $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Simple template rendering
function render($template, $data = []) {
    extract($data);
    ob_start();
    include dirname(__DIR__) . "/templates/layout/default.php";
    return ob_get_clean();
}

function renderPartial($template, $data = []) {
    extract($data);
    ob_start();
    include dirname(__DIR__) . "/templates/{$template}.php";
    return ob_get_clean();
}

// Simple controller dispatcher
try {
    $controllerFile = dirname(__DIR__) . "/src/Controller/{$controller}Controller.php";
    
    if (!file_exists($controllerFile)) {
        throw new Exception("Controller not found: {$controller}Controller");
    }
    
    require_once $controllerFile;
    
    $controllerClass = "App\\Controller\\{$controller}Controller";
    
    if (!class_exists($controllerClass)) {
        throw new Exception("Controller class not found: {$controllerClass}");
    }
    
    $controllerInstance = new $controllerClass();
    
    if (!method_exists($controllerInstance, $action)) {
        throw new Exception("Action not found: {$action} in {$controllerClass}");
    }
    
    // Call the action
    if ($id !== null) {
        echo $controllerInstance->$action($id);
    } else {
        echo $controllerInstance->$action();
    }
    
} catch (Exception $e) {
    // Simple error page
    http_response_code(404);
    echo "<h1>Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p><a href='/'>Go Home</a></p>";
}
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
if ($path === 'login' || $path === 'auth/login') {
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
} elseif (preg_match('/^admin\/students\/(\d+)\/monthly-studies\/(\d+)\/(\d+)$/', $path, $matches)) {
    // Handle monthly studies route: /admin/students/{id}/monthly-studies/{year}/{month}
    $controller = 'Admin/Students';
    $action = 'monthlyStudies';
    $id = $matches[1]; // student id
    $year = $matches[2];
    $month = $matches[3];
} elseif (preg_match('/^students\/(\d+)\/monthly-studies\/(\d+)\/(\d+)$/', $path, $matches)) {
    // Handle monthly studies route: /students/{id}/monthly-studies/{year}/{month}
    $controller = 'Students';
    $action = 'monthlyStudies';
    $id = $matches[1]; // student id
    $year = $matches[2];
    $month = $matches[3];
} elseif (strpos($path, 'site') === 0) {
    // Handle site routes: /site/{controller}/{action}/{id}
    $siteSegments = array_slice($segments, 1); // Remove 'site' from segments
    
    if (empty($siteSegments[0])) {
        // /site/ - redirect to home
        header('Location: /');
        exit;
    }
    
    $controllerName = ucfirst($siteSegments[0]);
    $controller = 'Site/' . $controllerName;
    $action = !empty($siteSegments[1]) ? $siteSegments[1] : 'index';
    $id = !empty($siteSegments[2]) ? $siteSegments[2] : null;
    
    // Convert kebab-case to camelCase for action names
    $action = lcfirst(str_replace('-', '', ucwords($action, '-')));
    
} elseif (strpos($path, 'admin') === 0) {
    // Handle admin routes: /admin/{controller}/{action}/{id}
    if ($path === 'admin' || $path === 'admin/') {
        // Root admin route - check user role and redirect appropriately
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['user_role'])) {
            if ($_SESSION['user_role'] === 'admin') {
                $controller = 'Admin/Students';
                $action = 'admin_dashboard';
                $id = null;
            } elseif ($_SESSION['user_role'] === 'student') {
                // Redirect students to their dashboard
                $controller = 'Admin/Students';
                $action = 'dashboard';
                $id = null;
            } else {
                // Unknown role, redirect to login
                header('Location: /login');
                exit;
            }
        } else {
            // Not logged in, redirect to login
            header('Location: /login');
            exit;
        }
    } else {
        $adminSegments = array_slice($segments, 1); // Remove 'admin' from segments
        $controller = 'Admin/' . (!empty($adminSegments[0]) ? ucfirst($adminSegments[0]) : 'Students');
        $rawAction = !empty($adminSegments[1]) ? $adminSegments[1] : 'index';
        
        // Handle special cases for admin routes
        if ($rawAction === 'courses-students') {
            $action = 'indexStudents';
        } else {
            $action = $rawAction === 'index' ? 'index' : lcfirst(str_replace('-', '', ucwords($rawAction, '-')));
        }
        
        $id = !empty($adminSegments[2]) ? $adminSegments[2] : null;
    }
} else {

    // Handle public site routes
    if (empty($path) || $path === '') {
    
        // This should never be reached, but keeping as fallback
        $controller = 'Site/Home';
        $action = 'index';
        $id = null;
    } elseif ($path === 'sobre') {
        $controller = 'Site/Home';
        $action = 'about';
        $id = null;
    } elseif ($path === 'mentoria') {
        $controller = 'Site/Mentoria';
        $action = 'index';
        $id = null;
    } elseif ($path === 'contato') {
        $controller = 'Site/Home';
        $action = 'contact';
        $id = null;
    } elseif ($path === 'courses') {
        $controller = 'Site/Courses';
        $action = 'index';
        $id = null;
    } else {
        // Default routing logic
        $controller = !empty($segments[0]) ? ucfirst($segments[0]) : 'Students';
        $action = !empty($segments[1]) ? $segments[1] : 'index';
        $id = !empty($segments[2]) ? $segments[2] : null;
        
        // Convert kebab-case to snake_case for action names
        $action = str_replace('-', '_', $action);
    }
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
    include dirname(__DIR__) . "/templates/{$template}.php";
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
    
    // Handle namespace for controllers
    if (strpos($controller, 'Admin/') === 0) {
        $controllerClass = "App\\Controller\\Admin\\" . substr($controller, 6) . "Controller";
    } elseif (strpos($controller, 'Site/') === 0) {
        $controllerClass = "App\\Controller\\Site\\" . substr($controller, 5) . "Controller";
    } else {
        $controllerClass = "App\\Controller\\{$controller}Controller";
    }
    
    if (!class_exists($controllerClass)) {
        throw new Exception("Controller class not found: {$controllerClass}");
    }
    
    $controllerInstance = new $controllerClass();
    
    if (!method_exists($controllerInstance, $action)) {
        throw new Exception("Action not found: {$action} in {$controllerClass}");
    }
    
    // Call the action
    if ($id !== null) {
        if (isset($year) && isset($month)) {
            // For monthly studies route with year and month parameters
            echo $controllerInstance->$action($id, $year, $month);
        } else {
            echo $controllerInstance->$action($id);
        }
    } else {
        echo $controllerInstance->$action();
    }
    
} catch (Exception $e) {
    // Simple error page
    http_response_code(500);
    echo "<h1>Error</h1>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Controller: " . htmlspecialchars($controller ?? 'unknown') . "</p>";
    echo "<p>Action: " . htmlspecialchars($action ?? 'unknown') . "</p>";
    echo "<p>Path: " . htmlspecialchars($path ?? 'unknown') . "</p>";
    echo "<p><a href='/admin/'>Go Home</a></p>";
}
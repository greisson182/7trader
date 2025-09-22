<?php
declare(strict_types=1);

namespace App\Controller;

/**
 * Application Controller
 *
 * Base controller for the application
 */
class AppController
{
    protected $db;
    protected $viewVars = [];
    
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = getDbConnection();
        $this->checkAuth();
    }
    
    protected function checkAuth()
    {
        // Páginas que não precisam de autenticação
        $publicPages = ['/login', '/logout'];
        $currentPath = $_SERVER['REQUEST_URI'];
        
        if (in_array($currentPath, $publicPages)) {
            return;
        }
        
        if (!$this->isLoggedIn()) {
            $this->redirect('/login');
        }
        
        // Disponibilizar dados do usuário para todos os templates
        $this->set('currentUser', $this->getCurrentUser());
    }
    
    protected function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    protected function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ? AND active = 1");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    
    protected function isAdmin()
    {
        $user = $this->getCurrentUser();
        return $user && $user['role'] === 'admin';
    }
    
    protected function isStudent()
    {
        $user = $this->getCurrentUser();
        return $user && $user['role'] === 'student';
    }
    
    protected function requireAdmin()
    {
        if (!$this->isAdmin()) {
            $this->flash('Acesso negado. Apenas administradores podem acessar esta página.', 'error');
            $this->redirect('/');
        }
    }
    
    protected function getCurrentStudentId()
    {
        $user = $this->getCurrentUser();
        if ($user && $user['role'] === 'student') {
            return $user['student_id'];
        }
        return null;
    }
    
    protected function login($username, $password)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND active = 1");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            return true;
        }
        
        return false;
    }
    
    protected function logout()
    {
        session_destroy();
        $this->redirect('/login');
    }
    
    protected function getDbConnection()
    {
        return $this->db;
    }
    
    protected function set($key, $value = null)
    {
        if (is_array($key)) {
            $this->viewVars = array_merge($this->viewVars, $key);
        } else {
            $this->viewVars[$key] = $value;
        }
    }
    
    protected function render($template, $vars = [])
    {
        $allVars = array_merge($this->viewVars, $vars);
        $content = renderPartial($template, $allVars);
        return render('layout/default', array_merge($allVars, ['content' => $content]));
    }
    
    protected function redirect($url)
    {
        // Handle array-based URLs (CakePHP style)
        if (is_array($url)) {
            $controller = isset($url['controller']) ? strtolower($url['controller']) : $this->getControllerName();
            $action = isset($url['action']) ? $url['action'] : 'index';
            $id = isset($url['id']) ? '/' . $url['id'] : '';
            
            $url = "/{$controller}/{$action}{$id}";
        }
        
        header("Location: $url");
        exit;
    }
    
    private function getControllerName()
    {
        $className = get_class($this);
        $className = str_replace('App\\Controller\\', '', $className);
        $className = str_replace('Controller', '', $className);
        return strtolower($className);
    }
    
    protected function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }
    
    protected function getData()
    {
        return $_POST;
    }
    
    protected function getPostData()
    {
        return $_POST;
    }
    
    protected function flash($message, $type = 'success')
    {
        $_SESSION['flash'] = ['message' => $message, 'type' => $type];
    }
    
    protected function getFlash()
    {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            unset($_SESSION['flash']);
            return $flash;
        }
        return null;
    }
}
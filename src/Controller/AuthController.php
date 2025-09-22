<?php
declare(strict_types=1);

namespace App\Controller;

class AuthController extends AppController
{
    public function __construct()
    {
        // Inicializar sessão e banco sem verificação de auth
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Load the global getDbConnection function from index.php
        if (!function_exists('getDbConnection')) {
            require_once ROOT . DS . 'webroot' . DS . 'index.php';
        }
        
        $this->db = getDbConnection();
        // Não chamar checkAuth() para páginas de login
    }
    
    public function loginAction()
    {
        // Se já está logado, redirecionar para home
        if ($this->isLoggedIn()) {
            $this->redirect('/');
        }
        
        if ($this->isPost()) {
            $data = $this->getPostData();

            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                $this->flash('Por favor, preencha todos os campos.', 'error');
            } else {
                if ($this->login($username, $password)) {
                 
                    $this->flash('Login realizado com sucesso!', 'success');
                    $this->redirect('/admin');
                } else {
                    $this->flash('Usuário ou senha inválidos.', 'error');
                }
            }
        }
        
        return renderPartial('Auth/login', $this->viewVars);
    }
    
    public function logoutAction()
    {
        $this->logout();
    }
}
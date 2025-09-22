<?php
declare(strict_types=1);

namespace App\Controller;

class AuthController extends AppController
{
    public function __construct()
    {
        // Não chamar parent::__construct() para evitar verificação de auth
        session_start();
        $this->db = getDbConnection();
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
                    $this->redirect('/');
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
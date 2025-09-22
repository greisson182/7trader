<?php
declare(strict_types=1);

namespace App\Controller\Admin;

use App\Controller\AppController;

/**
 * Base Admin Controller
 * 
 * Controlador base para área administrativa
 */
class AdminController extends AppController
{
    /**
     * Initialization hook method.
     */
    public function initialize(): void
    {
        parent::initialize();
        
        // Verificar se o usuário está logado
        if (!$this->request->getSession()->read('Auth.User')) {
            $this->Flash->error('Você precisa estar logado para acessar esta área.');
            return $this->redirect(['controller' => 'Auth', 'action' => 'login']);
        }
        
        // Verificar se é admin (se necessário no futuro)
        // $user = $this->request->getSession()->read('Auth.User');
        // if (!$user['is_admin']) {
        //     $this->Flash->error('Acesso negado.');
        //     return $this->redirect('/');
        // }
    }
}
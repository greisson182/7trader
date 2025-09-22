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
    public function __construct()
    {
        parent::__construct();
        
        // Verificar se o usuário está logado
        if (!$this->isLoggedIn()) {
            $this->flash('Você precisa estar logado para acessar esta área.', 'error');
            $this->redirect('/login');
        }
        
        // Permitir acesso tanto para admin quanto para estudantes
        // Cada controller específico pode implementar suas próprias restrições
        // usando $this->requireAdmin() quando necessário
    }
}
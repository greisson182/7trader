<?php
declare(strict_types=1);

namespace App\Controller\Site;

use App\Controller\AppController;
use PDO;
use Exception;

/**
 * Courses Controller for Students
 *
 * @property \App\Model\Table\CoursesTable $Courses
 */
class CoursesController extends AppController
{
    public function initialize(): void
    {
        parent::initialize();
        
        // Verificar se usuário está logado
        if (!$this->isLoggedIn()) {
            $this->flash('Você precisa estar logado para acessar os cursos.', 'error');
            $this->redirect('/auth/login');
            return;
        }
    }


}
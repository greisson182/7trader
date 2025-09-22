<?php
declare(strict_types=1);

namespace App\Controller\Site;

use App\Controller\AppController;

/**
 * Site Mentoria Controller
 * 
 * Controlador para a página de mentoria
 */
class MentoriaController extends AppController
{
    public function __construct()
    {
        // Não chamar parent::__construct() para evitar verificação de auth
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->db = getDbConnection();
    }
    
    /**
     * Initialization hook method.
     */
    public function initialize(): void
    {
        // Site público não precisa de autenticação
        // Não chamar parent::initialize() que força autenticação
    }
    
    /**
     * Index method - Página principal de mentoria
     */
    public function index()
    {
        $this->set([
            'title' => 'Mentoria - BackTest',
            'description' => 'Mentoria personalizada para traders - Acelere seu crescimento no mercado financeiro'
        ]);
        
        return $this->render('Site/Mentoria/index');
    }
    
    protected function getLayout()
    {
        // Usar layout do site público
        return 'layout/site';
    }
}
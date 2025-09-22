<?php
declare(strict_types=1);

namespace App\Controller\Site;

use App\Controller\AppController;

/**
 * Site Home Controller
 * 
 * Controlador para o site público
 */
class HomeController extends AppController
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
     * Index method - Página inicial do site
     */
    public function index()
    {
        $this->set([
            'title' => 'BackTest - Sistema de Análise de Trading',
            'description' => 'Plataforma profissional para análise e acompanhamento de resultados de trading'
        ]);
        
        return $this->render('Site/Home/index');
    }
    
    /**
     * About method - Sobre o sistema
     */
    public function about()
    {
        $this->set([
            'title' => 'Sobre - BackTest',
            'description' => 'Conheça mais sobre nossa plataforma de análise de trading'
        ]);
        
        return $this->render('Site/Home/about');
    }
    
    protected function getLayout()
    {
        // Usar layout do site público
        return 'layout/site';
    }
    
    /**
     * Contact method - Contato
     */
    public function contact()
    {
        // Simplificar o método contact removendo a verificação de POST por enquanto
        $this->set([
            'title' => 'Contato - BackTest',
            'description' => 'Entre em contato conosco'
        ]);
        
        return $this->render('Site/Home/contact');
    }
}
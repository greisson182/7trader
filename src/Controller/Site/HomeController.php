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
    /**
     * Initialization hook method.
     */
    public function initialize(): void
    {
        parent::initialize();
        
        // Site público não precisa de autenticação
        // Remover verificações de login se existirem
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
    
    /**
     * Contact method - Contato
     */
    public function contact()
    {
        if ($this->request->is('post')) {
            $data = $this->request->getData();
            
            // Aqui você pode implementar o envio de email
            // Por enquanto, apenas uma mensagem de sucesso
            $this->Flash->success('Mensagem enviada com sucesso! Entraremos em contato em breve.');
            return $this->redirect(['action' => 'contact']);
        }
        
        $this->set([
            'title' => 'Contato - BackTest',
            'description' => 'Entre em contato conosco'
        ]);
        
        return $this->render('Site/Home/contact');
    }
}
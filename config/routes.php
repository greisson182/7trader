<?php
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return static function (RouteBuilder $routes) {
    $routes->setRouteClass(DashedRoute::class);

    // ===========================================
    // SITE PÚBLICO
    // ===========================================
    $routes->scope('/', function (RouteBuilder $builder) {
        // Página inicial do site público
        $builder->connect('/', ['controller' => 'Site/Home', 'action' => 'index']);
        $builder->connect('/sobre', ['controller' => 'Site/Home', 'action' => 'about']);
        $builder->connect('/contato', ['controller' => 'Site/Home', 'action' => 'contact']);
        
        // Auth routes (public)
        $builder->connect('/login', ['controller' => 'Auth', 'action' => 'login']);
        $builder->connect('/logout', ['controller' => 'Auth', 'action' => 'logout']);
    });

    // ===========================================
    // ÁREA ADMINISTRATIVA
    // ===========================================
    $routes->scope('/admin', function (RouteBuilder $builder) {
        // Dashboard administrativo
        $builder->connect('/', ['controller' => 'Admin/Students', 'action' => 'dashboard']);
        $builder->connect('/dashboard', ['controller' => 'Admin/Students', 'action' => 'dashboard']);
        
        // Students routes (admin)
        $builder->connect('/students', ['controller' => 'Admin/Students', 'action' => 'index']);
        $builder->connect('/students/add', ['controller' => 'Admin/Students', 'action' => 'add']);
        $builder->connect('/students/edit/*', ['controller' => 'Admin/Students', 'action' => 'edit']);
        $builder->connect('/students/delete/*', ['controller' => 'Admin/Students', 'action' => 'delete']);
        
        // Manter rotas originais para compatibilidade (serão migradas gradualmente)
        $builder->connect('/students-old', ['controller' => 'Students', 'action' => 'index']);
        $builder->connect('/students-old/add', ['controller' => 'Students', 'action' => 'add']);
        $builder->connect('/students-old/view/*', ['controller' => 'Students', 'action' => 'view']);
        $builder->connect('/students-old/edit/*', ['controller' => 'Students', 'action' => 'edit']);
        $builder->connect('/students-old/delete/*', ['controller' => 'Students', 'action' => 'delete']);
        
        // Studies routes
        $builder->connect('/studies', ['controller' => 'Admin/Studies', 'action' => 'index']);
        $builder->connect('/studies/add', ['controller' => 'Admin/Studies', 'action' => 'add']);
        $builder->connect('/studies/view/*', ['controller' => 'Admin/Studies', 'action' => 'view']);
        $builder->connect('/studies/edit/*', ['controller' => 'Admin/Studies', 'action' => 'edit']);
        $builder->connect('/studies/delete/*', ['controller' => 'Admin/Studies', 'action' => 'delete']);
        
        // Markets routes
        $builder->connect('/markets', ['controller' => 'Admin/Markets', 'action' => 'index']);
        $builder->connect('/markets/add', ['controller' => 'Admin/Markets', 'action' => 'add']);
        $builder->connect('/markets/view/*', ['controller' => 'Admin/Markets', 'action' => 'view']);
        $builder->connect('/markets/edit/*', ['controller' => 'Admin/Markets', 'action' => 'edit']);
        $builder->connect('/markets/delete/*', ['controller' => 'Admin/Markets', 'action' => 'delete']);
        
        // Profile routes
        $builder->connect('/profile', ['controller' => 'Admin/Profile', 'action' => 'index']);
        $builder->connect('/profile/edit', ['controller' => 'Admin/Profile', 'action' => 'edit']);
        $builder->connect('/profile/update', ['controller' => 'Admin/Profile', 'action' => 'update']);
        
        // Metrics routes
        $builder->connect('/students/metrics/*', ['controller' => 'Students', 'action' => 'metrics']);
        
        // Dashboard routes (estudantes individuais)
        $builder->connect('/students/dashboard/*', ['controller' => 'Students', 'action' => 'dashboard']);
        
        // Monthly studies route
        $builder->connect('/students/*/monthly-studies/*/*', ['controller' => 'Students', 'action' => 'monthlyStudies']);
        
        $builder->fallbacks();
    });
};
<?php
use Cake\Routing\Route\DashedRoute;
use Cake\Routing\RouteBuilder;

return static function (RouteBuilder $routes) {
    $routes->setRouteClass(DashedRoute::class);

    $routes->scope('/', function (RouteBuilder $builder) {
        // Auth routes (public)
        $builder->connect('/login', ['controller' => 'Auth', 'action' => 'login']);
        $builder->connect('/logout', ['controller' => 'Auth', 'action' => 'logout']);
        
        // Default route (protected)
        $builder->connect('/', ['controller' => 'Students', 'action' => 'index']);
        
        // Students routes
        $builder->connect('/students', ['controller' => 'Students', 'action' => 'index']);
        $builder->connect('/students/add', ['controller' => 'Students', 'action' => 'add']);
        $builder->connect('/students/view/*', ['controller' => 'Students', 'action' => 'view']);
        $builder->connect('/students/edit/*', ['controller' => 'Students', 'action' => 'edit']);
        $builder->connect('/students/delete/*', ['controller' => 'Students', 'action' => 'delete']);
        
        // Studies routes
        $builder->connect('/studies', ['controller' => 'Studies', 'action' => 'index']);
        $builder->connect('/studies/add', ['controller' => 'Studies', 'action' => 'add']);
        $builder->connect('/studies/view/*', ['controller' => 'Studies', 'action' => 'view']);
        $builder->connect('/studies/edit/*', ['controller' => 'Studies', 'action' => 'edit']);
        $builder->connect('/studies/delete/*', ['controller' => 'Studies', 'action' => 'delete']);
        
        // Markets routes
        $builder->connect('/markets', ['controller' => 'Markets', 'action' => 'index']);
        $builder->connect('/markets/add', ['controller' => 'Markets', 'action' => 'add']);
        $builder->connect('/markets/view/*', ['controller' => 'Markets', 'action' => 'view']);
        $builder->connect('/markets/edit/*', ['controller' => 'Markets', 'action' => 'edit']);
        $builder->connect('/markets/delete/*', ['controller' => 'Markets', 'action' => 'delete']);
        
        // Metrics routes
        $builder->connect('/students/metrics/*', ['controller' => 'Students', 'action' => 'metrics']);
        
        // Dashboard routes
        $builder->connect('/students/dashboard/*', ['controller' => 'Students', 'action' => 'dashboard']);
        $builder->connect('/students/admin-dashboard', ['controller' => 'Students', 'action' => 'admin_dashboard']);
        
        // Monthly studies route
        $builder->connect('/students/*/monthly-studies/*/*', ['controller' => 'Students', 'action' => 'monthlyStudies']);
        
        $builder->fallbacks();
    });
};
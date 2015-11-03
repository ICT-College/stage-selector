<?php

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

Router::scope('/api/coordinator_approved_selector', ['plugin' => 'IctCollege/CoordinatorApprovedSelector'], function (RouteBuilder $routeBuilder) {
    $routeBuilder->extensions(['json']);
    $routeBuilder->resources('InternshipApplications', [
        'only' => ['index', 'create', 'positionDelete'],
        'map' => [
            'positionDelete' => [
                'action' => 'deletePosition',
                'method' => 'DELETE',
                'path' => 'position-delete'
            ]
        ]
    ]);
});
Router::prefix('admin', ['plugin' => 'IctCollege/CoordinatorApprovedSelector'], function (RouteBuilder $routeBuilder) {
    $routeBuilder->scope('/students/:student_id', ['plugin' => 'IctCollege/CoordinatorApprovedSelector'], function (RouteBuilder $routeBuilder) {
        $routeBuilder->connect('/internship-applications/:action/*', ['controller' => 'InternshipApplications']);
    });
});

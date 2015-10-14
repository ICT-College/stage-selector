<?php

\Cake\Routing\Router::scope('/api/coordinator_approved_selector', ['plugin' => 'IctCollege/CoordinatorApprovedSelector'], function (\Cake\Routing\RouteBuilder $routeBuilder) {
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

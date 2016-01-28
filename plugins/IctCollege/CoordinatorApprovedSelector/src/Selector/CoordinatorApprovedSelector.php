<?php

namespace IctCollege\CoordinatorApprovedSelector\Selector;

use Cake\Routing\RouteBuilder;
use Cake\Routing\Route\Route;

class CoordinatorApprovedSelector
{

    /**
     * {@inheritDoc}
     */
    public function setupRoutes(RouteBuilder $routeBuilder)
    {
        $routeBuilder->plugin('IctCollege/CoordinatorApprovedSelector', ['path' => '/'], function (RouteBuilder $routeBuilder) {
            $routeBuilder->connect('/periods/select/*', [
                'controller' => 'Periods',
                'action' => 'select'
            ], [
                '_name' => 'selector'
            ]);
        });

        return $routeBuilder;
    }
}

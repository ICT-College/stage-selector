<?php

namespace IctCollege\CoordinatorApprovedSelector\Selector;

use Cake\Routing\RouteBuilder;

class CoordinatorApprovedSelector
{

    /**
     * {@inheritDoc}
     */
    public function setupRoutes(RouteBuilder $routeBuilder)
    {
        $routeBuilder->connect('/', ['plugin' => 'IctCollege/CoordinatorApprovedSelector', 'controller' => 'Positions', 'action' => 'select'], ['_name' => 'selector']);

        return $routeBuilder;
    }
}

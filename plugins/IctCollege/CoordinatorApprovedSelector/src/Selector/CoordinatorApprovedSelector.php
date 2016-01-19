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
        $routeBuilder->connect('/', ['plugin' => 'IctCollege/CoordinatorApprovedSelector', 'controller' => 'Pages', 'action' => 'display', 'select'], ['_name' => 'selector']);

        return $routeBuilder;
    }
}

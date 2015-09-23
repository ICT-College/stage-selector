<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\Network\Response;
use Stagemarkt\Locator\RepositoryLocator;

class PositionsController extends AppController
{

    public function initialize()
    {
        parent::initialize();

        $this->modelFactory('Repository', [new RepositoryLocator, 'get']);

        $this->loadModel('Stagemarkt.Positions', 'Repository');

        $this->Crud->config('actions.index', [
            'className' => 'SearchIndex'
        ]);
    }
}

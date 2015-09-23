<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\Network\Response;
use Stagemarkt\Locator\RepositoryLocator;

class CompaniesController extends AppController
{

    public function initialize()
    {
        parent::initialize();

        $this->modelFactory('Repository', [new RepositoryLocator, 'get']);

        $this->loadModel('Stagemarkt.Companies', 'Repository');

        $this->Crud->config('actions.index', [
            'className' => 'SearchIndex'
        ]);
    }
}

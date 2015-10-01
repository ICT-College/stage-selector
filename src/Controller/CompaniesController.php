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

        $this->Crud->config('actions.index', [
            'className' => 'SearchIndex'
        ]);
    }
}

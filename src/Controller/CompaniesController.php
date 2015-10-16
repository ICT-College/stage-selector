<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\Network\Response;

class CompaniesController extends AppController
{

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->Crud->config('actions.index', [
            'className' => 'SearchIndex'
        ]);
    }
}

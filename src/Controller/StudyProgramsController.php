<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\Network\Response;
use Stagemarkt\Locator\RepositoryLocator;

class StudyProgramsController extends AppController
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

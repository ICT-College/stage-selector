<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\Network\Response;
use Cake\ORM\Query;
use Stagemarkt\Locator\RepositoryLocator;

class PositionsController extends AppController
{

    public function initialize()
    {
        parent::initialize();

        $this->Crud->config('actions.index', [
            'className' => 'SearchIndex'
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.beforePaginate' => 'beforePaginate'
        ];
    }

    public function beforePaginate(Event $event)
    {
        /* @var Query $query */
        $query = $event->subject()->query;

        $query->contain([
            'Companies',
            'StudyPrograms',
        ]);
    }
}

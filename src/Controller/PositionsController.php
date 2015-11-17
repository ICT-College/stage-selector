<?php

namespace App\Controller;

use Cake\Event\Event;
use Cake\Network\Response;
use Cake\ORM\Query;

class PositionsController extends AppController
{

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.beforeFind' => 'beforeFindQuery',
            'Crud.beforePaginate' => 'beforeFindQuery'
        ];
    }

    /**
     * Adds contain to the query to get relations
     *
     * @param Event $event Event that was dispatched
     *
     * @return void
     */
    public function beforeFindQuery(Event $event)
    {
        /* @var Query $query */
        $query = $event->subject()->query;

        $query->contain([
            'Companies',
            'StudyPrograms',
            'QualificationParts'
        ]);
    }
}

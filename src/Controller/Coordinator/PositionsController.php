<?php
namespace App\Controller\Coordinator;

use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\ORM\Query;
use CvoTechnologies\Gearman\JobAwareTrait;

class PositionsController extends AppController
{

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.beforeFind' => 'beforeFindQuery',
            'Crud.beforePaginate' => 'beforeFindQuery',
            'Crud.beforeSave' => 'beforeSave'
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

<?php
namespace App\Controller\Coordinator;

use Cake\Event\Event;
use Cake\ORM\TableRegistry;

class InternshipsController extends AppController
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
        /* @var \Cake\ORM\Query $query */
        $query = $event->subject()->query;

        $query->contain([
            'Periods',
            'Positions' => [
                'Companies',
                'StudyPrograms',
            ],
            'Users',
        ]);
    }
}

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

    public function beforeSave(Event $event)
    {
        $this->loadModel('Internships');

        /* @var \Cake\ORM\Entity $entity */
        $entity = $event->subject()->entity;

        $internship = $this->Internships->find('active', [
            'student' => $this->Auth->user('student_id')
        ])
            ->contain([
                'Periods'
            ])
            ->firstOrFail();

        $entity->amount = 1;

        $entity->start = $internship->period->start;
        $entity->end = $internship->period->end;
    }
}

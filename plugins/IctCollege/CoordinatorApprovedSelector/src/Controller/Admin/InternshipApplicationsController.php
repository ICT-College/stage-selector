<?php

namespace IctCollege\CoordinatorApprovedSelector\Controller\Admin;

use Cake\Event\Event;

class InternshipApplicationsController extends AppController
{

    /**
     * @inheritDoc
     */
    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.beforePaginate' => 'beforePaginate'
        ];
    }

    public function beforePaginate(Event $event)
    {
        /* @var \Cake\ORM\Query $query */
        $query = $event->subject()->query;

        $query->andWhere([
            'student_id' => $this->request->param('student_id')
        ]);

        $query->contain([
            'Positions' => [
                'Companies',
                'StudyPrograms'
            ]
        ]);
    }
}

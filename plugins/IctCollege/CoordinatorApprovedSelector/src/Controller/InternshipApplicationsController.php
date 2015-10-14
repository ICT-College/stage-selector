<?php

namespace IctCollege\CoordinatorApprovedSelector\Controller;

use Cake\Event\Event;

/**
 * @property \IctCollege\CoordinatorApprovedSelector\Model\Table\InternshipApplicationsTable InternshipApplications
 */
class InternshipApplicationsController extends AppController
{

    public function deletePosition()
    {
        $application = $this->InternshipApplications->find()->where([
            'position_id' => $this->request->data('position_id'),
            'student_id' => $this->Auth->user('student_id')
        ])->firstOrFail();

        ;

        $this->set('success', (bool)$this->InternshipApplications->delete($application));
        $this->set('_serialize', ['success']);
    }

    /**
     * @inheritDoc
     */
    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.beforePaginate' => 'beforePaginate',
            'Crud.beforeSave' => 'beforeSave'
        ];
    }

    public function beforePaginate(Event $event)
    {
        /* @var \Cake\ORM\Query $query */
        $query = $event->subject()->query;

        $query->contain([
            'Positions' => [
                'Companies',
                'StudyPrograms'
            ]
        ]);
    }

    public function beforeSave(Event $event)
    {
        /* @var \Cake\ORM\Entity $entity */
        $entity = $event->subject()->entity;

        $entity->student_id = $this->Auth->user('student_id');
    }
}

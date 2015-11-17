<?php

namespace IctCollege\CoordinatorApprovedSelector\Controller;

use Cake\Event\Event;
use Cake\Network\Exception\BadRequestException;

/**
 * @property \IctCollege\CoordinatorApprovedSelector\Model\Table\InternshipApplicationsTable InternshipApplications
 */
class InternshipApplicationsController extends AppController
{

    public function deletePosition()
    {
        $application = $this->InternshipApplications->find()->where([
            'position_id' => $this->request->data('position_id'),
            'student_id' => $this->Auth->user('student_id'),
        ])->firstOrFail();

        if ($application->accepted_coordinator) {
            throw new BadRequestException(__('Accepted application can not be removed'));
        }

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

        $studentId = $this->Auth->user('student_id');
        $internship = $this->InternshipApplications->Periods->Internships->find('active', [
            'student' => $studentId
        ])->firstOrFail();

        $query->where([
            'student_id' => $studentId,
            'period_id' => $internship->period_id
        ]);

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

        $studentId = $this->Auth->user('student_id');
        $internship = $this->InternshipApplications->Periods->Internships->find('active', [
            'student' => $studentId
        ])->firstOrFail();

        $entity->period_id = $internship->period_id;
        $entity->student_id = $studentId;
    }
}

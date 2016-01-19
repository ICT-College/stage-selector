<?php

namespace IctCollege\CoordinatorApprovedSelector\Controller;

use Cake\Event\Event;
use Cake\Network\Exception\BadRequestException;
use Cake\Network\Exception\InternalErrorException;

/**
 * @property \IctCollege\CoordinatorApprovedSelector\Model\Table\InternshipApplicationsTable InternshipApplications
 */
class InternshipApplicationsController extends AppController
{

    /**
     * {@inheritDoc}
     */
    public function submit()
    {
        $this->loadModel('Users');

        $internship = $this->InternshipApplications->Periods->Internships
            ->find('active', [
                'student' => $this->Auth->user('student_id')
            ])
            ->contain([
                'Users',
                'Periods'
            ])
            ->firstOrFail();

        $internshipApplications = $this->InternshipApplications->find('all')->where([
            'student_id' => $internship->user->student_id,
            'period_id' => $internship->period->id
        ])->contain([
            'Positions' => [
                'Companies',
                'StudyPrograms'
            ],
        ])->toArray();

        if (!$this->InternshipApplications->submit($internship->user, $internship, $internshipApplications)) {
            throw new InternalErrorException();
        }

        $this->Flash->success(__('Thank you for submitting your applications, you\'ve received an e-mail with further information.'));

        $this->set('internship', $internship);
        $this->set('internshipApplications', $internshipApplications);
        $this->set('success', true);
        $this->set('_serialize', ['internship', 'internshipApplications', 'success']);
    }

    /**
     * {@inheritDoc}
     */
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
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        return parent::implementedEvents() + [
            'Crud.beforePaginate' => 'beforePaginate',
            'Crud.beforeSave' => 'beforeSave'
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function beforePaginate(Event $event)
    {
        /* @var \Cake\ORM\Query $query */
        $query = $event->subject()->query;

        $studentId = $this->Auth->user('student_id');
        $internship = $this->InternshipApplications->Periods->Internships->find('active', [
            'student' => $studentId
        ])->contain([
            'Periods'
        ])->firstOrFail();

        $this->set('period', $internship->period);

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

    /**
     * {@inheritDoc}
     */
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

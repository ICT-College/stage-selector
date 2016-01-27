<?php

namespace App\Controller;


use Cake\Event\Event;
use Cake\ORM\Query;

/**
 * @property \App\Model\Table\InternshipsTable Internships
 */
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

    public function accept($id)
    {
        $internship = $this->Internships->get($id);

        if (!$this->Internships->acceptStudent($internship)) {
            $this->Flash->error(__('Could not mark internship as accepted'));

            return $this->redirect(['action' => 'view', $id]);
        }

        $this->Flash->success(__('Your internship has been accepted'));

        return $this->redirect(['action' => 'view', $id]);
    }

    public function planInterview($id)
    {
        $internship = $this->Internships->get($id);

        if (!$this->Internships->planInterview($internship, $this->request->data('planned_interview_date'))) {
            $this->Flash->error(__('Could not save your interview date'));

            return $this->redirect([
                'action' => 'view',
                $id
            ]);
        }

        $this->Flash->success(__('Thank you for planning your interview'));

        $this->redirect([
            'action' => 'view',
            $id
        ]);
    }

    public function interview($id)
    {
        $internship = $this->Internships->get($id);

        $this->set('internship', $internship);

        if (!$this->request->is('put')) {
            return;
        }

        if (!$internship = $this->Internships->markInterviewed($internship, $this->request->data())) {
            $this->Flash->error(__('Could not save your report.'));

            return;
        }

        $this->Flash->success(__('Your report has been saved.'));

        $this->redirect([
            'action' => 'view',
            $id
        ]);
    }

    public function report($id)
    {
        $internship = $this->Internships->get($id);

        $this->response->file(dirname(APP) . DS . $internship->report_dir . $internship->report);
        $this->response->type($internship->report_type);

        return $this->response;
    }

    public function select()
    {
        $this->loadModel('Users');

        $user = $this->Users->get($this->Auth->user('id'), [
            'contain' => [
                'Internships'
            ]
        ]);

        $this->set('internships', $user->internships);
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
            'Positions' => [
                'Companies',
                'StudyPrograms',
                'QualificationParts'
            ]
        ]);
    }
}

<?php

namespace App\Controller;


use Cake\Event\Event;
use Cake\ORM\Query;

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

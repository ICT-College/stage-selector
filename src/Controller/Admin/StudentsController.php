<?php
namespace App\Controller\Admin;

use App\Form\InviteStudentForm;
use App\Form\StudentsSyncForm;
use Cake\Cache\Cache;
use Cake\I18n\Time;

class StudentsController extends AppController
{

    public function initialize()
    {
        parent::initialize();

        $this->modelClass = null;
    }

    public function synchronize()
    {
        $studentsSync = new StudentsSyncForm();

        if ($this->request->is('post')) {
            if ($studentsSync->execute($this->request->data)) {

                Cache::write('students_sync', new Time());

                $this->Flash->success(__('Successfully inserted/updated all students from the CSV.'));
            } else {
                $this->Flash->error(__('There is a problem while importing students.'));
            }
        }

        return $this->redirect($this->referer());
    }

    public function invite()
    {
        $inviteStudent = new InviteStudentForm();

        if ($this->request->is('post')) {
            if ($inviteStudent->execute($this->request->data)) {

                Cache::write('students_sync', new Time());

                $this->Flash->success(__('User is invited.'));
            } else {
                $this->Flash->error(__('There was a problem while inviting this student.'));
            }
        }

        return $this->redirect($this->referer());
    }
}

<?php
namespace App\Controller\Admin;

use App\Form\InviteStudentForm;
use App\Form\StudentsSyncForm;
use Cake\Cache\Cache;
use Cake\I18n\Time;

class StudentsController extends AppController
{
    private $studentsSync;
    private $inviteStudent;

    public function initialize()
    {
        parent::initialize();

        $this->studentsSync = new StudentsSyncForm();
        $this->inviteStudent = new InviteStudentForm();

        $this->modelClass = null;
    }


    public function index()
    {
        $lastStudentsSync = Cache::read('students_sync');

        $this->set('studentsSync', $this->studentsSync);
        $this->set('inviteStudent', $this->inviteStudent);
        $this->set('lastStudentsSync', $lastStudentsSync);
    }

    public function studentsSync()
    {
        if ($this->request->is('post')) {
            if ($this->studentsSync->execute($this->request->data)) {

                Cache::write('students_sync', new Time());

                $this->Flash->success(__('Successfully inserted or updaten all students from the CSV.'));
            } else {
                $this->Flash->error(__('There is a problem while importing students.'));
            }
        }

        return $this->setAction('index');
    }

    public function inviteStudent()
    {
        if ($this->request->is('post')) {
            if ($this->inviteStudent->execute($this->request->data)) {

                Cache::write('students_sync', new Time());

                $this->Flash->success(__('User is invited.'));
            } else {
                $this->Flash->error(__('There was a problem while inviting this student.'));
            }
        }

        return $this->setAction('index');
    }
}

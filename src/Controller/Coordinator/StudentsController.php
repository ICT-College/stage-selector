<?php
namespace App\Controller\Coordinator;

use App\Form\InviteStudentForm;
use App\Form\StudentsSyncForm;
use Cake\Cache\Cache;
use Cake\Database\Expression\Comparison;
use Cake\Event\Event;
use Cake\I18n\Time;

class StudentsController extends AppController
{

    public $paginate = [
        'conditions' => [
            'student_id IS NOT' => null
        ]
    ];

    public function initialize()
    {
        parent::initialize();

        $this->modelClass = 'Users';
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

        if (!$query->clause('where')) {
            return;
        }

        $comparisons = [];
        $studentId = false;
        $query->clause('where')->traverse(function (Comparison $comparison) use ($comparisons, &$studentId) {
            if ($comparison->getField() !== 'Users.id') {
                $comparisons[] = $comparison;

                return;
            }

            $studentId = $comparison->getValue();
        });

        if ($studentId) {
            $query->where($comparisons, [], true);
            $query->andWhere([
                'Users.student_id' => $studentId
            ]);
        }
    }
}

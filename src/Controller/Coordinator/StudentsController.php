<?php
namespace App\Controller\Coordinator;

use App\Form\InviteStudentForm;
use App\Form\StudentsSyncForm;
use App\ShardAwareTrait;
use Cake\Cache\Cache;
use Cake\Database\Expression\Comparison;
use Cake\Event\Event;
use Cake\I18n\Time;
use Cake\ORM\TableRegistry;

class StudentsController extends AppController
{

    use ShardAwareTrait;

    /**
     * {@inheritDoc}
     */
    public function initialize()
    {
        parent::initialize();

        $this->modelClass = 'Users';
    }

    /**
     * {@inheritDoc}
     */
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

    /**
     * {@inheritDoc}
     */
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

        $hasWhere = $query->clause('where');

        $query->matching('Shards', function ($q) {
            return $q->where(['Shards.id' => $this->shard()->id]);
        }) ->where([
            'Users.student_id IS NOT' => null
        ]);

        $query->contain([
            'Internships' => [
                'Periods',
                'Positions' => [
                    'Companies',
                    'StudyPrograms'
                ]
            ]
        ]);

        if (!$hasWhere) {
            return;
        }

        $expressions = [];
        $studentId = false;
        $query->clause('where')->traverse(function ($expression) use ($expressions, &$studentId) {
            if (!$expression instanceof Comparison || $expression->getField() !== 'Users.id') {
                $expressions[] = $expression;
                return;
            }

            $studentId = $expression->getValue();
        });

        if ($studentId) {
            $query->where($expressions, [], true);
            $query->andWhere([
                'Users.student_id' => $studentId
            ]);
        }
    }
}

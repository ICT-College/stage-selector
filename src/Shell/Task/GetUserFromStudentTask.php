<?php

namespace App\Shell\Task;

use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use Cake\ORM\Entity;
use Psr\Log\LogLevel;

/**
 * @property \App\Model\Table\UsersTable Users
 */
class GetUserFromStudentTask extends Shell
{

    /**
     * Updates the detailsx of a company
     *
     * @param array $workload Options to use in task
     *
     * @return \App\Model\Entity\User|bool
     */
    public function main(array $workload)
    {
        $this->loadModel('Users');

        ConnectionManager::alias($workload['shard']->secured_datasource, 'secured');

        $this->loadModel('Students');

        // Get student using student ID
        $student = $this->Students->find()->select([
            'id',
            'firstname',
            'insertion',
            'lastname',
            'student_number',
            'email',
            'learning_pathway',
            'study_program_id',
            'groupcode'
        ])->where([
            'student_number' => $workload['student_number']
        ])->first();
        if (!$student) {
            $this->log(__('Could not find student with number {0}', $workload['student_number']), LogLevel::WARNING);

            return false;
        }

        $conditions = ['student_id' => $student->id];
        if (($this->Users->exists($conditions))) {
            $user = $this->Users->find()->where($conditions)->first();
        } else {
            $user = $this->Users->newEntity($conditions, [
                'validate' => false
            ]);
        }

        /* @var \App\Model\Entity\User $user */
        if ($user->isNew()) {
            $this->log(__('User entity initialized based on student entity with id {0}', $student->id), LogLevel::INFO);
        } else {
            $this->log(__('User entity updated using student entity with id {0}', $student->id), LogLevel::INFO);
        }

        return $this->Users->patchEntity($user, [
            'firstname' => $student->firstname,
            'insertion' => $student->insertion,
            'lastname' => $student->lastname,
            'student_number' => $student->student_number,
            'email' => $student->email,
            'learning_pathway' => $student->learning_pathway,
            'study_program_id' => $student->study_program_id,
            'groupcode' => $student->groupcode,
        ], [
            'validate' => false
        ]);
    }
}

<?php
namespace App\Controller\Admin;

use Cake\ORM\TableRegistry;
use CvoTechnologies\Gearman\JobAwareTrait;

class UsersController extends AppController
{

    use JobAwareTrait;

    /**
     * Invite a student to the system using a job.
     *
     * @param int $number Student number to invite
     * @return null
     */
    public function invite($number)
    {
        $shard = TableRegistry::get('Shards')->get(1);
        $user = $this->Users->fromStudent($number, $shard);
        if (!$user) {
            $this->Flash->error(__('Failed to receive user from student.'));

            return $this->redirect($this->referer());
        }

        $user = $this->Users->invite($user, $shard);
        if (!$user) {
            $this->Flash->error(__('Failed to invite user.'));

            return $this->redirect($this->referer());
        }

        $this->Flash->success(__('Student is invited.'));

        return $this->redirect($this->referer());
    }
}

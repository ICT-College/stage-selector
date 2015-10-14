<?php
namespace App\Controller\Admin;

use Cake\ORM\TableRegistry;
use CvoTechnologies\Gearman\JobAwareTrait;

class UsersController extends AppController
{

    use JobAwareTrait;

    public function invite($number)
    {
        $shard = TableRegistry::get('Shards')->get(1);
        $user = $this->Users->fromStudent($number, $shard);
        if (!$user) {
            return;
        }

        $user = $this->Users->invite($user, $shard);
        if (!$user) {
            $this->set('user', $user);

            return;
        }
    }
}

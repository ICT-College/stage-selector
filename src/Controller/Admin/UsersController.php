<?php
namespace App\Controller\Admin;

use Cake\ORM\TableRegistry;
use CvoTechnologies\Gearman\JobAwareTrait;

class UsersController extends AppController
{

    use JobAwareTrait;

    public function index()
    {
        $action = $this->Crud->action();
        $action->config('scaffold.fields', [
            'student_number',
            'name' => [
                'formatter' => function($name, $value, $entity) {
                    return h($entity->name);
                }
            ],
            'email'
        ]);
        return $this->Crud->execute();
    }

    public function view()
    {
        $action = $this->Crud->action();
        $action->config('scaffold.fields', [
            'student_number',
            'name' => [
                'formatter' => function($name, $value, $entity) {
                    return h($entity->name);
                }
            ],
            'email'
        ]);
        return $this->Crud->execute();
    }

    public function edit()
    {
        $action = $this->Crud->action();
        $action->config('scaffold.disable_extra_buttons', true);
        $action->config('scaffold.fields', [
            'student_number',
            'firstname',
            'insertion',
            'lastname',
            'email'
        ]);
        return $this->Crud->execute();
    }

    public function add()
    {
        $action = $this->Crud->action();
        $action->config('scaffold.disable_extra_buttons', true);
        $action->config('scaffold.fields', [
            'student_number',
            'firstname',
            'insertion',
            'lastname',
            'email'
        ]);
        return $this->Crud->execute();
    }

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

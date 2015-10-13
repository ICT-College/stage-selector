<?php
namespace App\Controller\Admin;

class UsersController extends AppController
{

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
}

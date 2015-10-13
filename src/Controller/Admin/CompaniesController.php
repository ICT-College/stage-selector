<?php
namespace App\Controller\Admin;

class CompaniesController extends AppController
{

    public function initialize()
    {
        parent::initialize();

        $this->Crud->config('actions.add', null);
    }

    public function index()
    {
        $action = $this->Crud->action();
        $action->config('scaffold.fields', [
            'id',
            'name',
            'address' => [
                'formatter' => function ($name, $value, $entity) {
                    return h($entity->address) . '<br/>' . h($entity->postcode . ' ' . $entity->city);
                }
            ],
            'email',
            'website',
            'telephone'
        ]);
        return $this->Crud->execute();
    }

    public function edit()
    {
        $action = $this->Crud->action();
        $action->config('scaffold.disable_extra_buttons', true);
        $action->config('scaffold.fields_blacklist', [
            'stagemarkt_id',
            'created',
            'modified'
        ]);
        return $this->Crud->execute();
    }
}

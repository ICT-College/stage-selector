<?php
namespace App\Controller\Admin;

use Cake\Datasource\ConnectionManager;
use CvoTechnologies\Gearman\JobAwareTrait;

class CompaniesController extends AppController
{

    use JobAwareTrait;

    public function initialize()
    {
        parent::initialize();

        if ($this->request->action === 'index') {
            $this->Crud->addListener('Crud.Search');

            $this->loadComponent('Search.Prg');
        }

        $this->Crud->config('actions.add', null);
    }

    public function index()
    {
        $action = $this->Crud->action();
        $action->config('scaffold', [
            'fields' => [
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
            ],
            'extra_actions' => [
                'updateDetails',
                'updateCoordinates'
            ]
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

    public function updateDetails($id)
    {
        $company = $this->Companies->get($id);

        $this->execute('company_details', [
            'company_id' => $id,
            'datasource' => ConnectionManager::get('default')->configName()
        ], false);

        if ($this->gearmanClient()->returnCode() === GEARMAN_WORK_FAIL) {
            $this->Flash->error(__('The details of company {0} could not be updated', $company->name));

            return $this->redirect($this->request->referer());
        }

        $this->Flash->success(__('The details of company {0} have been updated', $company->name));

        return $this->redirect($this->request->referer());
    }

    public function updateCoordinates($id)
    {
        $company = $this->Companies->get($id);

        $this->execute('company_coordinates', [
            'company_id' => $id,
            'datasource' => ConnectionManager::get('default')->configName()
        ], false);

        if ($this->gearmanClient()->returnCode() === GEARMAN_WORK_FAIL) {
            $this->Flash->error(__('The coordinates of company {0} could not be updated', $company->name));

            return $this->redirect($this->request->referer());
        }

        $this->Flash->success(__('The coordinates of company {0} have been updated', $company->name));

        return $this->redirect($this->request->referer());
    }
}

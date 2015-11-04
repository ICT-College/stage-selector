<?php
namespace App\Controller\Coordinator;

use Cake\Datasource\ConnectionManager;
use CvoTechnologies\Gearman\JobAwareTrait;

class CompaniesController extends AppController
{

    use JobAwareTrait;

    public function initialize()
    {
        parent::initialize();
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

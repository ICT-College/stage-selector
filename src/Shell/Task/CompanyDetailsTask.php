<?php

namespace App\Shell\Task;

use App\Model\Entity\Company;
use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use CvoTechnologies\Gearman\JobAwareTrait;
use Muffin\Webservice\Model\EndpointRegistry;
use Psr\Log\LogLevel;

class CompanyDetailsTask extends Shell
{

    use JobAwareTrait;

    /**
     * Updates the detailsx of a company
     *
     * @param array $options Options to use in task
     *
     * @return void
     */
    public function main($options)
    {
        if (!is_array($options)) {
            return;
        }

        ConnectionManager::alias($options['datasource'], 'default');

        $this->loadModel('Companies');

        $companiesEndpoint = EndpointRegistry::get('IctCollege/Stagemarkt.Companies');

        try {
            /* @var Company $company */
            $company = $this->Companies->get($options['company_id']);
        } catch (\Exception $exception) {
            if (!isset($options['retried'])) {
                $options['retried'] = 1;
            } else {
                $options['retried']++;
            }
            if ($options['retried'] > 5) {
                return;
            }

            $this->log(__('Rescheduling lookup of details of company with id {0}. Retries: {1}', $options['company_id'], $options['retried']), LogLevel::WARNING);

            $this->execute('company_coordinates', $options);

            return;
        }

        $this->log(__('Looking up details for company {0} with id {1}', $company->name, $company->id), LogLevel::INFO);

        $companyResource = $companiesEndpoint->get($company->stagemarkt_id);

        if (!$companyResource) {
            $this->log(__('Looking up details for company {0} with id {1} failed', $company->name, $company->id), LogLevel::NOTICE);

            return;
        }

        $fields = [
            'email',
            'website',
            'telephone'
        ];
        foreach ($fields as $field) {
            if (empty($field)) {
                continue;
            }
            if ($company->get($field) === $companyResource->get($field)) {
                continue;
            }

            $company->set($field, $companyResource->get($field));
        }

        if (!$this->Companies->save($company)) {
            return;
        }
    }
}

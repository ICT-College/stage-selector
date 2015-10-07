<?php

namespace App\Shell\Task;

use App\Model\Entity\Company;
use Cake\Console\Shell;
use Cake\Datasource\ConnectionManager;
use CvoTechnologies\Gearman\JobAwareTrait;
use Psr\Log\LogLevel;

class CompanyCoordinatesTask extends Shell
{

    use JobAwareTrait;

    public function main($options)
    {
        if (!is_array($options)) {
            return;
        }

        ConnectionManager::alias($options['datasource'], 'default');

        $this->loadModel('Companies');

        try {
            /* @var Company $company */
            $company = $this->Companies->get($options['company_id']);
        }
        catch (\Exception $exception) {
            if (!isset($options['retried'])) {
                $options['retried']  = 1;
            } else {
                $options['retried']++;
            }
            if ($options['retried'] > 5) {
                return;
            }

            $this->log(__('Rescheduling lookup for coordinates of company with id {0}. Retries: {1}', $options['company_id'], $options['retried']), LogLevel::WARNING);

            $this->execute('company_coordinates', $options);

            return;
        }

        $this->log(__('Looking up coordinates for company {0} with id {1}', $company->name, $company->id), LogLevel::INFO);

        $coordinates = $company->addressToCoordinates($company->address . ' ' . $company->city);
        if (!$coordinates) {
            $this->log(__('Looking up coordinates for company {0} with id {1} failed', $company->name, $company->id), LogLevel::NOTICE);

            return;
        }

        $company->coordinates = $coordinates;

        if (!$this->Companies->save($company)) {
            return;
        }
    }
}

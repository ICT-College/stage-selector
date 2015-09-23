<?php

namespace Stagemarkt\Repository;

use Cake\Database\Schema\Table;
use Cake\Datasource\ConnectionManager;
use Stagemarkt\WebserviceQuery;

class PositionsRepository extends Repository
{

    use SearchableTrait;

    public $filterArgs = array(
        'company_id' => array(
            'type' => 'value'
        ),
        'company_name' => array(
            'type' => 'like'
        ),
        'company_address_address' => array(
            'type' => 'value'
        ),
        'company_address_postcode' => array(
            'type' => 'value'
        ),
        'company_address_city' => array(
            'type' => 'like'
        ),
        'company_address_country' => array(
            'type' => 'value'
        ),
        'study_program_id' => array(
            'type' => 'value'
        ),
        'learning_pathway' => array(
            'type' => 'value'
        ),
        'description' => array(
            'type' => 'like'
        ),
    );

    public function initialize(array $config)
    {
        parent::initialize($config);

        $schema = new Table(null, [
            'id' => [
                'type' => 'string'
            ],
            'name' => [
                'type' => 'string'
            ]
        ]);
        $schema->addConstraint('primary', [
            'type' => Table::CONSTRAINT_PRIMARY,
            'columns' => 'id'
        ]);
        $this->schema($schema);
        $this->webservice(ConnectionManager::get('Stagemarkt')->searchClient());
    }

    /**
     * Creates a new Query instance for this repository
     *
     * @return WebserviceQuery
     */
    public function query()
    {
        $query = parent::query();

        $query->conditions([
            'type' => 'position'
        ]);

        return $query;
    }
}

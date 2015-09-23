<?php

namespace Stagemarkt\Repository;

use Cake\Database\Schema\Table;
use Cake\Datasource\ConnectionManager;
use Stagemarkt\WebserviceQuery;

class CompaniesRepository extends Repository
{

    use SearchableTrait;

    public $filterArgs = array(
        'name' => array(
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
     * @return \Stagemarkt\WebserviceQuery
     */
    public function query()
    {
        $query = parent::query();

        $query->conditions([
            'type' => 'company'
        ]);

        return $query;
    }
}

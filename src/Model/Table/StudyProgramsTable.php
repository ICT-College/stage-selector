<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class StudyProgramsTable extends Table
{

    public $filterArgs = [
        'q' => [
            'type' => 'like',
            'field' => 'description'
        ],
    ];

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Searchable');
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultConnectionName()
    {
        return 'main';
    }
}

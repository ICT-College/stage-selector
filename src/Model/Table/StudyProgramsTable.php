<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class StudyProgramsTable extends Table
{

    public $filterArgs = array(
        'q' => array(
            'type' => 'like',
            'field' => 'description'
        ),
    );

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Searchable');
    }

    public static function defaultConnectionName()
    {
        return 'main';
    }
}

<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Search\Manager;

class StudyProgramsTable extends Table
{

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Timestamp');
        $this->addBehavior('Search.Search');
        $this->hasMany('QualificationParts');
    }

    public function searchConfiguration()
    {
        $search = new Manager($this);
        $search->like('q', [
            'before' => true,
            'after' => true,
            'field' => $this->aliasField('description')
        ]);
        return $search;
    }

    /**
     * {@inheritDoc}
     */
    public static function defaultConnectionName()
    {
        return 'main';
    }
}

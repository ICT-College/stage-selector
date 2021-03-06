<?php

namespace App\Model\Table;

use Cake\ORM\Table;
use Search\Manager;

class StudyProgramsTable extends Table
{

    /**
     * {@inheritDoc}
     */
    public static function defaultConnectionName()
    {
        return 'main';
    }

    /**
     * {@inheritDoc}
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Timestamp');
        $this->addBehavior('CachedAssociation');
        $this->addBehavior('Search.Search');

        $this->hasMany('QualificationParts');
    }

    /**
     * {@inheritDoc}
     */
    public function searchConfiguration()
    {
        $search = new Manager($this);
        $search->like('q', [
            'before' => true,
            'after' => true,
            'field' => [
                $this->aliasField('id'),
                $this->aliasField('description')
            ]
        ]);
        return $search;
    }
}

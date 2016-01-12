<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class QualificationPartsTable extends Table
{

    /**
     * Connection name for this Table
     *
     * @return string
     */
    public static function defaultConnectionName()
    {
        return 'main';
    }

    /**
     * @inheritDoc
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('CachedAssociation');
    }
}

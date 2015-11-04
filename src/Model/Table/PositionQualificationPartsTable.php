<?php

namespace App\Model\Table;

use Cake\ORM\Association;
use Cake\ORM\Table;

class PositionQualificationPartsTable extends Table
{

    /**
     * @inheritDoc
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->belongsTo('Positions');
        $this->belongsTo('QualificationParts');
    }
}

<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class PositionsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->addBehavior('Timestamp');

        $this->belongsTo('Companies');
    }
}

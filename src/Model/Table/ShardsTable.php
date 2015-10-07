<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class ShardsTable extends Table
{

    /**
     * {@inheritDoc}
     */
    public static function defaultConnectionName()
    {
        return 'main';
    }
}

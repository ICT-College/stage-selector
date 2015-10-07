<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class ShardsTable extends Table
{

    public static function defaultConnectionName()
    {
        return 'main';
    }
}

<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class ShardsUsersTable extends Table
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
}
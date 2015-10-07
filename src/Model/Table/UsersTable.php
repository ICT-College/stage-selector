<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class UsersTable extends Table
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

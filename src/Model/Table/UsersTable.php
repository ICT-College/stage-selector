<?php
namespace App\Model\Table;

use Cake\ORM\Table;

class UsersTable extends Table {

    public static function defaultConnectionName() {
        return 'main';
    }

}

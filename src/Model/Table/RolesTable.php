<?php

namespace App\Model\Table;

use Cake\ORM\Table;

class RolesTable extends Table
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
     * @param array $config
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->hasMany('Users');

        $this->addBehavior('Acl.Acl', [
            'type' => 'requester'
        ]);
    }
}

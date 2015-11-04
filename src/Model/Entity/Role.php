<?php

namespace App\Model\Entity;

use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\MissingDatasourceConfigException;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Exception;

class Role extends Entity
{

    /**
     * parentNode
     *
     * @return array|null
     */
    public function parentNode() {
        return null;
    }
}

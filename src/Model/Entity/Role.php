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

    /**
     * bindNode
     *
     * @param $ref
     *
     * @return array
     */
    public function bindNode($ref) {
        if (isset($ref['parent_id'])) {
            return $ref;
        }

        $parent_id = null;

        try {
            $connection = ConnectionManager::get('default');

            $shardTable = TableRegistry::get('Shards');
            $shard = $shardTable->find()->where([
                'datasource' => $connection->config()['name']
            ])->firstOrFail();

            $parent_id = $shard->id;
        } catch (MissingDatasourceConfigException $e) {
            // When default isn't set, we want the $parent_id remain NULL without showing an error to the visitor.
        }

        return [
            'parent_id' => $parent_id,
            'model' => 'Roles',
            'foreign_key' => $ref['Roles']['id']
        ];
    }
}

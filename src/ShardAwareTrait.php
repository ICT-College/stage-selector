<?php

namespace App;

use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\MissingDatasourceConfigException;
use Cake\ORM\TableRegistry;

trait ShardAwareTrait
{

    /**
     * @return \App\Model\Entity\Shard|null
     */
    public function shard()
    {
        try {
            $connection = ConnectionManager::get('default');

            $shardTable = TableRegistry::get('Shards');
            return $shardTable->find()->where([
                'datasource' => $connection->config()['name']
            ])->first();
        } catch (MissingDatasourceConfigException $e) {
        }

        return null;
    }

    /**
     * @return string
     */
    public function shardSubdomain()
    {
        $shard = $this->shard();
        if ($shard) {
            return $shard->subdomain;
        }

        return 'main';
    }
}

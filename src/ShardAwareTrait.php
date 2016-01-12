<?php

namespace App;

use App\Model\Entity\Shard;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\MissingDatasourceConfigException;
use Cake\ORM\TableRegistry;

trait ShardAwareTrait
{

    /**
     * @var \App\Model\Entity\Shard
     */
    protected $_shard;

    /**
     * @return \App\Model\Entity\Shard|null|$this
     */
    public function shard(Shard $shard = null)
    {
        if ($shard) {
            $this->_shard = $shard;

            return $this;
        }

        try {
            $connection = ConnectionManager::get('default');

            $shardTable = TableRegistry::get('Shards');
            return $shardTable
                ->find()->where([
                    'datasource' => $connection->config()['name']
                ])
                ->cache('shard_' . $connection->config()['name'])
                ->first();
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

    public function useShardDatabase()
    {
        $shard = $this->shard();

        ConnectionManager::dropAlias('default');
        ConnectionManager::dropAlias('secured');

        ConnectionManager::alias($shard->datasource, 'default');
        ConnectionManager::alias($shard->secured_datasource, 'secured');

        TableRegistry::clear();
    }
}

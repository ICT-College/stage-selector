<?php

namespace App;

use App\Model\Entity\Shard;
use Cake\Cache\Cache;
use Cake\Core\App;
use Cake\Datasource\ConnectionManager;
use Cake\Datasource\Exception\MissingDatasourceConfigException;
use Cake\ORM\TableRegistry;
use DebugKit\DebugTimer;

trait ShardAwareTrait
{

    /**
     * @var \App\Model\Entity\Shard
     */
    protected $_shard;

    /**
     * @var Object
     */
    protected $_selector;

    /**
     * @return \App\Model\Entity\Shard|null|$this
     */
    public function shard(Shard $shard = null)
    {
        if ($shard) {
            $this->_shard = $shard;

            return $this;
        }

        if ($this->_shard) {
            return $this->_shard;
        }

        try {
            $connectionName = ConnectionManager::get('default')->config()['name'];

            $shard = Cache::read('shard_' . $connectionName);
            if ($shard === false) {
                DebugTimer::start('ShardAwareTrait: Looking up shard - ' . $connectionName);

                $shard = TableRegistry::get('Shards')
                    ->find()->where([
                        'datasource' => $connectionName
                    ])
                    ->first();

                DebugTimer::stop('ShardAwareTrait: Looking up shard - ' . $connectionName);

                Cache::write('shard_' . $connectionName, $shard);
            }

            return $this->_shard = $shard;
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

    public function shardSelector()
    {
        if ($this->_selector) {
            return $this->_selector;
        }
        if (!$this->shard()) {
            return false;
        }

        DebugTimer::start('ShardAwareTrait: ' . __FUNCTION__);

        $className = App::className($this->shard()->selector, 'Selector', 'Selector');

        $selector = new $className;

        DebugTimer::stop('ShardAwareTrait: ' . __FUNCTION__);

        return $this->_selector = $selector;
    }

    public function useShardDatabase()
    {
        DebugTimer::start('ShardAwareTrait: ' . __FUNCTION__);

        $shard = $this->shard();

        ConnectionManager::dropAlias('default');
        ConnectionManager::dropAlias('secured');

        ConnectionManager::alias($shard->datasource, 'default');
        ConnectionManager::alias($shard->secured_datasource, 'secured');

        TableRegistry::clear();

        DebugTimer::stop('ShardAwareTrait: ' . __FUNCTION__);
    }
}

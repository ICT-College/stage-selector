<?php

namespace App\Routing\Filter;

use App\ShardAwareTrait;
use Cake\Cache\Cache;
use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Network\Request;
use Cake\ORM\TableRegistry;
use Cake\Routing\DispatcherFilter;
use DebugKit\DebugTimer;

class ShardFilter extends DispatcherFilter
{

    use ShardAwareTrait;

    /**
     * Default priority for all methods in this filter
     *
     * @var int
     */
    protected $_priority = 9;

    /**
     * {@inheritDoc}
     */
    public function implementedEvents()
    {
        EventManager::instance()->on('Router.selectorRoute', [], [$this, 'selectorRoute']);

        return parent::implementedEvents();
    }

    /**
     * Handles the Router.selectorRoute event
     *
     * @param Event $event The event to be handled
     *
     * @return void
     */
    public function selectorRoute(Event $event)
    {
        DebugTimer::start('ShardFilter: ' . __FUNCTION__);

        if ($this->shardSelector()) {
            $this->shardSelector()->setupRoutes($event->subject());
        }

        DebugTimer::stop('ShardFilter: ' . __FUNCTION__);
    }

    /**
     * Sets up database connection based on subdomain
     *
     * @param Event $event event containing request data
     *
     * @return void
     */
    public function beforeDispatch(Event $event)
    {
        DebugTimer::start('ShardFilter: ' . __FUNCTION__);

        /* @var Request $request */
        $request = $event->data['request'];

        $subdomains = $request->subdomains();

        if (!isset($subdomains[0])) {
            return;
        }

        $shard = Cache::read('shard_subdomain_' . $subdomains[0]);
        if ($shard === false) {
            $shard = TableRegistry::get('Shards')
                ->find()->where([
                    'subdomain' => $subdomains[0]
                ])
                ->firstOrFail();

            Cache::write('shard_subdomain_' . $subdomains[0], $shard);
        }

        $this->shard($shard);
        $this->useShardDatabase();

        DebugTimer::stop('ShardFilter: ' . __FUNCTION__);
    }
}

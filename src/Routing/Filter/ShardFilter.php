<?php

namespace App\Routing\Filter;

use Cake\Datasource\ConnectionManager;
use Cake\Event\Event;
use Cake\Network\Request;
use Cake\Routing\DispatcherFilter;

class ShardFilter extends DispatcherFilter
{

    /**
     * Sets up database connection based on subdomain
     *
     * @param Event $event event containing request data
     *
     * @return void
     */
    public function beforeDispatch(Event $event)
    {
        /* @var Request $request */
        $request = $event->data['request'];

        ConnectionManager::alias('shard_' . $request->subdomains()[0], 'default');
    }
}

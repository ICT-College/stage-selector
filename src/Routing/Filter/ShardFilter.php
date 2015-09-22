<?php

namespace App\Routing\Filter;

use Cake\Event\Event;
use Cake\Datasource\ConnectionManager;
use Cake\Network\Request;
use Cake\Routing\DispatcherFilter;

class ShardFilter extends DispatcherFilter
{

    public function beforeDispatch(Event $event)
    {
        /** @var Request $request */
        $request = $event->data['request'];

        ConnectionManager::alias('shard_' . $request->subdomains()[0], 'default');
    }
}

<?php

namespace App\Routing\Filter;

use App\Model\Table\ShardsTable;
use Cake\Core\App;
use Cake\Datasource\ConnectionManager;
use Cake\Error\Debugger;
use Cake\Event\Event;
use Cake\Event\EventManager;
use Cake\Network\Request;
use Cake\ORM\TableRegistry;
use Cake\Routing\DispatcherFilter;
use Cake\Routing\RouteBuilder;

class ShardFilter extends DispatcherFilter
{

    /**
     * Default priority for all methods in this filter
     *
     * @var int
     */
    protected $_priority = 9;

    protected $_selector;

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
        if($this->_selector != null) {
            $this->_selector->setupRoutes($event->subject());
        }
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
        /* @var Request $request */
        $request = $event->data['request'];

        $subdomains = $request->subdomains();

        if (!isset($subdomains[0])) {
            return;
        }

        /* @var ShardsTable $shardsTable */
        $shardsTable = TableRegistry::get('Shards');
        $shard = $shardsTable->find()->where([
            'subdomain' => $subdomains[0]
        ])->firstOrFail();

        $className = App::className($shard->selector, 'Selector', 'Selector');

        $this->_selector = new $className();

        ConnectionManager::alias($shard->datasource, 'default');
    }
}

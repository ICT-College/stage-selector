<?php

namespace Stagemarkt;

use Cake\Core\InstanceConfigTrait;
use Cake\Database\Log\QueryLogger;
use Stagemarkt\Soap\Search;

class Stagemarkt
{

    use InstanceConfigTrait;

    protected $_defaultConfig = [
        'testing' => false
    ];

    protected $_logger;

    /**
     * @var Search
     */
    private $__searchClient;

    public function __construct(array $options)
    {
        $this->config($options);
    }

    public function logger(QueryLogger $logger = null)
    {
        $this->searchClient()->logger($logger);
    }

    public function search()
    {
        return $this->searchClient()->search();
    }

    /**
     * @return Search
     */
    public function searchClient()
    {
        if (!$this->__searchClient) {
            $this->__searchClient = new Search($this->config());
            $this->__searchClient->stagemarktClient($this);
        }

        return $this->__searchClient;
    }
}

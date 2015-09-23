<?php

namespace Stagemarkt;

use Cake\Core\InstanceConfigTrait;
use Cake\Database\Log\QueryLogger;
use Stagemarkt\Soap\Details;
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

    /**
     * @var Details
     */
    private $__detailsClient;

    public function __construct(array $options)
    {
        $this->config($options);
    }

    public function logger(QueryLogger $logger = null)
    {
        $this->searchClient()->logger($logger);
        $this->detailsClient()->logger($logger);
    }

    public function detailsForPosition($position)
    {
        return $this->detailsClient()->details([
            'position' => $position
        ]);
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

    /**
     * @return Details
     */
    public function detailsClient()
    {
        if (!$this->__detailsClient) {
            $this->__detailsClient = new Details($this->config());
            $this->__detailsClient->stagemarktClient($this);
        }

        return $this->__detailsClient;
    }
}

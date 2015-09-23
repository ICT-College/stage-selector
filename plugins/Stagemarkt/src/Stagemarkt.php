<?php

namespace Stagemarkt;

use Cake\Core\InstanceConfigTrait;
use Stagemarkt\Soap\Search;

class Stagemarkt
{

    use InstanceConfigTrait;

    protected $_defaultConfig = [
        'testing' => false
    ];

    private $_searchClient;

    public function __construct(array $options)
    {
        $this->config($options);
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
        if (!$this->_searchClient) {
            $this->_searchClient = new Search($this->config());
        }

        return $this->_searchClient;
    }
}

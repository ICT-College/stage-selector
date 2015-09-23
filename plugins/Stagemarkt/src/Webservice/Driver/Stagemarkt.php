<?php

namespace Stagemarkt\Webservice\Driver;

use Cake\Database\Log\QueryLogger;
use Muffin\Webservice\AbstractDriver;

class Stagemarkt extends AbstractDriver
{

    protected $_defaultConfig = [
        'testing' => false
    ];

    /**
     * Whether to log queries generated during this connection.
     *
     * @var bool
     */
    protected $_logQueries = false;

    /**
     * Logger object instance.
     *
     * @var QueryLogger
     */
    protected $_logger = null;

    /**
     * Initialize is used to easily extend the constructor.
     *
     * @return void
     */
    public function initialize()
    {
        $this->_client = new \Stagemarkt\Stagemarkt($this->config());
    }

    public function configName()
    {
        if (empty($this->_config['name'])) {
            return '';
        }
        return $this->_config['name'];
    }

    public function logQueries($enable = null)
    {
        if ($enable === null) {
            return $this->_logQueries;
        }
        $this->_logQueries = $enable;
    }

    public function logger($instance = null)
    {
        if ($instance === null) {
            if ($this->_logger === null) {
                $this->_logger = new QueryLogger;
            }
            return $this->_logger;
        }
        $this->_logger = $instance;
    }
}

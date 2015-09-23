<?php

namespace Stagemarkt\Soap;

use Cake\Database\Log\QueryLogger;
use DebugKit\DebugTimer;
use Stagemarkt\LoggedQuery;
use Stagemarkt\WebserviceInterface;

abstract class SoapWebservice extends SoapClient implements WebserviceInterface
{

    protected $_logger;

    /**
     * @param QueryLogger|null $logger
     * @return QueryLogger|$this
     */
    public function logger(QueryLogger $logger = null)
    {
        if ($logger === null) {
            return $this->_logger;
        }

        $this->_logger = $logger;

        return $this;
    }

    public function __soapCall($function_name, $arguments, $options = [], $input_headers = [], &$output_headers = [])
    {
        $query = new LoggedQuery;
        $query->query = $function_name;
        $query->params = $arguments;

        DebugTimer::start('stagemarkt-' . $function_name);

        $response = parent::__soapCall($function_name, $arguments, $options, $input_headers, $output_headers);

        DebugTimer::stop('stagemarkt-' . $function_name);

        $query->took = DebugTimer::elapsedTime('stagemarkt-' . $function_name) * 1000;

        $this->logger()->log($query);

        return $response;
    }
}

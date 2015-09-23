<?php

namespace Stagemarkt;

use Cake\Database\Log\QueryLogger;

interface WebserviceInterface
{

    public function logger(QueryLogger $logger = null);

    public function execute(WebserviceQuery $query);
}

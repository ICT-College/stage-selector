<?php

namespace Stagemarkt;

interface Webservice
{

    public function execute(WebserviceQuery $query);
}

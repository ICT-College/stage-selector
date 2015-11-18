<?php

namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

class Period extends Entity
{

    public function _getTitle()
    {
        return __('{0} untill {1}',  $this->start->toDateString(), $this->end->toDateString());
    }
}

<?php

namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

class Period extends Entity
{

    public function _getTitle()
    {
        return $this->start->toDateString() . ' t/m ' . $this->end->toDateString();
    }
}

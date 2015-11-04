<?php
namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

class Shard extends Entity
{

    /**
     * parentNode
     *
     * @return null
     */
    public function parentNode()
    {
        return null;
    }
}

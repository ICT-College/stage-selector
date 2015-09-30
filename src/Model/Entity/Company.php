<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Stagemarkt\Entity\Entity as StagemarktEntity;
use Stagemarkt\Entity\StagemarktBasedEntityTrait;

class Company extends Entity
{

    use StagemarktBasedEntityTrait {
        applyStagemarktEntity as protected _applyStagemarktEntity;
    }

    public function applyStagemarktEntity(StagemarktEntity $entity)
    {
        $this->set([
            'stagemarkt_id' => $entity->id,
            'name' => $entity->name,
            'address' => $entity->address->address,
            'postcode' => $entity->address->postcode,
            'city' => $entity->address->city,
        ]);

        if ($entity->address->country === 'Nederland') {
            $this->set('country', 'NL');
        }
    }
}

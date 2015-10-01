<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Stagemarkt\Entity\Entity as StagemarktEntity;
use Stagemarkt\Entity\StagemarktBasedEntityTrait;

class StudyProgram extends Entity
{

    use StagemarktBasedEntityTrait {
        applyStagemarktEntity as protected _applyStagemarktEntity;
    }

    public function applyStagemarktEntity(StagemarktEntity $entity) {
        $this->set([
            'id' => $entity->id,
            'description' => $entity->description,
        ]);
    }
}

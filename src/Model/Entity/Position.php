<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Stagemarkt\Entity\Entity as StagemarktEntity;
use Stagemarkt\Entity\StagemarktBasedEntityTrait;

class Position extends Entity
{

    use StagemarktBasedEntityTrait {
        applyStagemarktEntity as protected _applyStagemarktEntity;
    }

    public function applyStagemarktEntity(StagemarktEntity $entity) {
        $this->set([
            'stagemarkt_id' => $entity->id,
            'kind' => $entity->kind,
            'description' => $entity->description,
            'start' => $entity->start,
            'end' => $entity->end,
        ]);

        if ($entity->has('learning_pathway')) {
            $this->set('learning_pathway', $entity->learning_pathway);
        }
        if ($entity->has('study_program')) {
            $this->set('study_program_id', $entity->study_program->id);
        }
    }
}

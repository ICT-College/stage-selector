<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Muffin\Webservice\Model\Resource;
use Muffin\Webservice\Model\ResourceBasedEntityTrait;
use Stagemarkt\Entity\Entity as StagemarktEntity;
use Stagemarkt\Entity\StagemarktBasedEntityTrait;

class Position extends Entity
{

    use ResourceBasedEntityTrait {
        applyResource as protected _applyResource;
    }

    public function applyResource(Resource $resource) {
        $this->set([
            'stagemarkt_id' => $resource->id,
            'kind' => $resource->kind,
            'description' => $resource->description,
            'start' => $resource->start,
            'end' => $resource->end,
            'amount' => $resource->amount
        ]);

        if ($resource->has('learning_pathway')) {
            $this->set('learning_pathway', $resource->learning_pathway);
        }
        if ($resource->has('study_program')) {
            $this->set('study_program_id', $resource->study_program->id);
        }
    }
}

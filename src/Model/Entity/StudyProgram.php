<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Muffin\Webservice\Model\Resource;
use Muffin\Webservice\Model\ResourceBasedEntityTrait;
use Stagemarkt\Entity\Entity as StagemarktEntity;
use Stagemarkt\Entity\StagemarktBasedEntityTrait;

class StudyProgram extends Entity
{

    use ResourceBasedEntityTrait {
        applyResource as protected _applyResource;
    }

    public function applyResource(Resource $resource) {
        $this->set([
            'id' => $resource->id,
            'description' => $resource->description,
        ]);
    }
}

<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Muffin\Webservice\Model\Resource;
use Muffin\Webservice\Model\ResourceBasedEntityTrait;

class StudyProgram extends Entity
{

    use ResourceBasedEntityTrait {
        applyResource as protected _applyResource;
    }

    /**
     * {@inheritDoc}
     */
    public function applyResource(Resource $resource)
    {
        $this->set([
            'id' => $resource->id,
            'description' => $resource->description,
        ]);
    }
}

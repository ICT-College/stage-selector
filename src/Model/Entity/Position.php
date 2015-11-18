<?php

namespace App\Model\Entity;

use Cake\ORM\Entity;
use Muffin\Webservice\Model\Resource;
use Muffin\Webservice\Model\ResourceBasedEntityTrait;

class Position extends Entity
{

    protected $_virtual = ['available'];

    use ResourceBasedEntityTrait {
        applyResource as protected _applyResource;
    }

    protected function _getAvailable()
    {
        return $this->amount - ($this->internship_count + $this->internship_application_count);
    }

    /**
     * {@inheritDoc}
     */
    public function applyResource(Resource $resource)
    {
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

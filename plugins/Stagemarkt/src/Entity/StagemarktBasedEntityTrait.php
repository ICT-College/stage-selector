<?php

namespace Stagemarkt\Entity;

trait StagemarktBasedEntityTrait
{

    public static function createFromStagemarktEntity(Entity $stagemarktEntity, array $options = [])
    {
        $entity = new self();

        $entity->applyStagemarktEntity($stagemarktEntity);

        return $entity;
    }

    public function applyStagemarktEntity(Entity $entity)
    {
        $this->set($entity->toArray());
    }
}

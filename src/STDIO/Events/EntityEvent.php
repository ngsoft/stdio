<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Events;

use NGSOFT\STDIO\Entities\Entity;

abstract class EntityEvent extends Event
{

    public static function create(Entity $entity): static
    {
        return new static($entity);
    }

    public function __construct(
            protected Entity $entity
    )
    {

    }

    protected function getMethod(): string
    {
        return 'on' . str_replace('Entity', '', class_basename($this));
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }

    /**
     * Gets triggered even if propagation has been stopped
     */
    public function onEvent(): static
    {

        $entity = $this->getEntity();
        $method = $this->getMethod();

        // lazy
        if (method_exists($entity, $method)) {
            call_user_func([$entity, $method]);
        }


        return $this;
    }

}

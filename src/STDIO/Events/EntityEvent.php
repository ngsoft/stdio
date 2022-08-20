<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Events;

use NGSOFT\STDIO\Entities\Entity;

class EntityEvent extends Event
{

    public function create(Entity $entity): static
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
        call_user_func([$this->getEntity(), $this->getMethod()]);
        return $this;
    }

}

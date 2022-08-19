<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Events;

use NGSOFT\STDIO\Entities\Entity;

class EntityEvent extends Event
{

    public function __construct(
            public Entity $entity
    )
    {

    }

    public function onEvent(): static
    {

        if ( ! $this->propagationStopped) {

        }


        return $this;
    }

}

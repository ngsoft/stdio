<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Events;

use NGSOFT\Traits\StoppableEventTrait,
    Psr\EventDispatcher\StoppableEventInterface;

abstract class Event implements StoppableEventInterface
{

    use StoppableEventTrait;
}

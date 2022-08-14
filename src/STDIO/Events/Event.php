<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Events;

use NGSOFT\Traits\StoppableEventTrait,
    Psr\EventDispatcher\StoppableEventInterface;

class Event implements StoppableEventInterface
{

    use StoppableEventTrait;
}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Events;

class Event implements \Psr\EventDispatcher\StoppableEventInterface
{

    use \NGSOFT\Traits\StoppableEventTrait;
}

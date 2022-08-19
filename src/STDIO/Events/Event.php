<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Events;

/**
 * Base Event for STDIO
 */
abstract class Event extends \NGSOFT\Events\Event
{

    /**
     * Gets executed after event propagation
     */
    abstract public function onEvent(): static;
}

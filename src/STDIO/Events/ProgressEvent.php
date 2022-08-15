<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Events;

use NGSOFT\STDIO\Helpers\ProgressBar;

abstract class ProgressEvent extends Event
{

    public function __construct(
            protected ProgressBar $progressBar
    )
    {

    }

    /**
     * Gets executed when event is triggered
     */
    abstract public function onEvent(): void;

    public function getProgressBar(): ProgressBar
    {
        return $this->progressBar;
    }

}

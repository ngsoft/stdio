<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

use NGSOFT\{
    STDIO\Styles\Styles, Traits\DispatcherAware
};

class ProgressBar
{

    use DispatcherAware;

    public function __construct(
            protected ?Styles $styles
    )
    {
        $this->styles ??= new Styles();
    }

}

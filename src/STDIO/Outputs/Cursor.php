<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Outputs;

final class Cursor
{

    public function __construct(
            protected ?Output $output = null
    )
    {
        $this->output ??= new Output();
    }

}

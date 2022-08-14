<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Outputs;

use NGSOFT\Console\Outputs\Output;

interface Renderer
{

    /**
     * Render to the Output
     */
    public function render(Output $output): void;
}

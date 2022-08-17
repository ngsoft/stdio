<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Outputs;

interface Renderer
{

    /**
     * Render to the Output
     */
    public function render(OutputInterface $output): void;
}

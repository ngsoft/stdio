<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Outputs;

use Stringable;

interface OutputInterface {

    /**
     * Writes a message to the output.
     *
     * @param string|Stringable|string[] $message
     * @return void
     */
    public function write(string|Stringable|array $message): void;
}

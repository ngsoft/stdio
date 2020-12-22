<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Interfaces;

interface Output {

    /**
     * Writes a message to the output.
     * @param string $message
     */
    public function write(string $message);
}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Inputs;

interface InputInterface {

    /**
     * Read lines from the input
     * @param int $lines Number of lines to read
     * @return array<string>
     */
    public function read(int $lines = 1): array;
}

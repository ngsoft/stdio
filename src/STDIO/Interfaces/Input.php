<?php

namespace NGSOFT\STDIO\Interfaces;

interface Input {

    /**
     * Read lines from the input
     * @param int $lines Number of lines to read
     * @return array<string>
     */
    public function read(int $lines = 1): array;
}

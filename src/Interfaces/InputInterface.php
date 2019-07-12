<?php

namespace NGSOFT\Tools\Interfaces;

interface InputInterface {

    /**
     * Read lines from the input
     * @param int $lines Number of lines to read
     * @return array<string>
     */
    public function read(int $lines = 1): array;

    /**
     * Read a single line from the Input
     * @return string
     */
    public function readln(): string;
}

<?php

namespace NGSOFT\Tools\IO\Inputs;

use InvalidArgumentException;

class STDIN extends Input {

    private $stream;

    public function __construct() {
        $this->stream = fopen("php://stdin", "r");
    }

    /** {@inheritdoc} */
    public function read(int $lines = 1): array {
        if (0 > $lines) throw new InvalidArgumentException("Number of lines cannot be negative");
        $result = [];
        do {
            $line = fgets($this->stream);
            $result[] = rtrim($line, "\r\n");
        } while (count($result) !== $lines);
        return $result;
    }

    /**
     * Get The Resource directly
     * @return resource
     */
    public function getStream() {
        return $this->stream;
    }

}

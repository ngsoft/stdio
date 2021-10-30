<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Inputs;

use NGSOFT\STDIO\Interfaces\{
    Input, Stream
};

class StreamInput implements Stream, Input {

    /** @var resource */
    private $stream;

    public function __construct() {
        $this->stream = fopen('php://stdin', 'r');
    }

    /** {@inheritdoc} */
    public function read(int $lines = 1): array {
        $result = [];
        do {
            $line = fgets($this->stream);
            $line = rtrim($line, "\r\n");
            $result[] = rtrim($line, "\r\n");
        } while (count($result) !== $lines);
        return $result;
    }

    /** {@inheritdoc} */
    public function getStream() {
        return $this->stream;
    }

}

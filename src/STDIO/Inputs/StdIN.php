<?php

namespace NGSOFT\STDIO\Inputs;

use NGSOFT\STDIO\Interfaces\{
    Input, Stream
};

class StdIN implements Input, Stream {

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
            $result[] = rtrim($line, "\r\n");
        } while (count($result) !== $lines);
        return $result;
    }

    /** {@inheritdoc} */
    public function getStream() {
        return $this->stream;
    }

    /** {@inheritdoc} */
    public function withStream($stream) {
        assert(is_resource($stream));
        $i = clone $this;
        $i->stream = $stream;
        return $i;
    }

}

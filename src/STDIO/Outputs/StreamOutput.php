<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Outputs;

use NGSOFT\STDIO\Interfaces\{
    Formatter, Output, Stream
};
use RuntimeException;

class StreamOutput implements Stream, Output {

    /** @var Formatter */
    protected $formatter;

    /** @var resource|null */
    protected $stream;

    public function __construct() {
        $this->stream = fopen('php://stdout', 'w');
    }

    /** {@inheritdoc} */
    public function write(string $message) {
        if (false === @fwrite($this->stream, $message)) {
            throw new RuntimeException('Unable to write output.');
        }
        fflush($this->stream);
    }

    /** {@inheritdoc} */
    public function getStream() {
        return $this->stream;
    }

}

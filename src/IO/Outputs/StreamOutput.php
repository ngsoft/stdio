<?php

namespace NGSOFT\Tools\IO\Outputs;

use NGSOFT\Tools\IO\Formatters\{
    DefaultFormatter, Formatter
};
use RuntimeException;

class StreamOutput extends Output {

    /** @var resource */
    protected $stream;

    public function __construct($stream) {
        assert(is_resource($stream) && get_resource_type($stream) === "stream");
        $this->stream = $stream;
    }

    /** {@inheritdoc} */
    protected function doWrite(string $message, bool $newline) {
        $message .= $newline === true ? PHP_EOL : "";

        if (false === @fwrite($this->stream, $message)) {
            throw new RuntimeException('Unable to write output.');
        }
        fflush($this->stream);
    }

}

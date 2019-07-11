<?php

namespace NGSOFT\Tools\IO\Outputs;

use NGSOFT\Tools\IO\Formatters\{
    DefaultFormatter, Formatter
};
use RuntimeException;

abstract class StreamOutput extends Output {

    /** @var resource */
    private $stream;

    public function __construct($stream, Formatter $formatter = null) {

        assert(is_resource($stream) && get_resource_type($stream) === "stream");
        $this->stream = $stream;
        if ($formatter === null) $formatter = new DefaultFormatter();
        parent::__construct($formatter);
    }

    /**
     * @return resource
     */
    public function getStream() {
        return $this->stream;
    }

    protected function doWrite(string $message, bool $newline) {
        $message .= $newline === true ? PHP_EOL : "";

        if (false === @fwrite($this->stream, $message)) {
            throw new RuntimeException('Unable to write output.');
        }
        fflush($this->stream);
    }

}

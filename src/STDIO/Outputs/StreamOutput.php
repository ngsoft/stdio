<?php

namespace NGSOFT\STDIO\Outputs;

class StreamOutput implements Stream, Output {

    /** @var Formatter */
    protected $formatter;

    /** @var resource|null */
    protected $stream;

    public function __construct() {
        $this->stream = fopen('php://stdout', 'w');
    }

    /** {@inheritdoc} */
    public function getFormatter(): Formatter {
        return $this->formatter;
    }

    /** {@inheritdoc} */
    public function setFormatter(Formatter $formatter) {
        $this->formatter = $formatter;
    }

    /** {@inheritdoc} */
    public function withFormatter(Formatter $formatter) {
        $i = clone $this;
        $i->formatter = $formatter;
        return $i;
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

    /** {@inheritdoc} */
    public function withStream($stream) {
        assert(is_resource($stream));
        $i = clone $this;
        $i->stream = $stream;
        return $i;
    }

}

<?php

namespace NGSOFT\STDIO\Outputs;

use NGSOFT\STDIO\Interfaces\{
    Buffer, Output
};

class OutputBuffer implements Buffer {

    /** @var string[] */
    private $buffer = [];

    /** {@inheritdoc} */
    public function clear() {
        $this->buffer = [];
    }

    /** {@inheritdoc} */
    public function flush(Output $output) {
        foreach ($this->buffer as $message) $output->write($message);
        $this->clear();
    }

    /** {@inheritdoc} */
    public function write(string $message) {
        $this->buffer[] = $message;
    }

    /** {@inheritdoc} */
    public function getBuffer(): array {
        return $this->buffer;
    }

}

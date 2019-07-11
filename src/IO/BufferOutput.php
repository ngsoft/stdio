<?php

namespace NGSOFT\Tools\IO;

class BufferOutput extends Output {

    /** @var string */
    private $buffer = "";

    /**
     * Empties the buffer and return its content
     * @return string
     */
    public function fetch(): string {
        $content = $this->buffer;
        $this->buffer = "";
        return $content;
    }

    protected function doWrite(string $message, bool $newline) {
        $this->buffer .= $newline;
        if ($newline === true) $this->buffer .= PHP_EOL;
    }

}

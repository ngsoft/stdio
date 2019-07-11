<?php

namespace NGSOFT\Tools\IO\Outputs;

use NGSOFT\Tools\IO\Formatters\Formatter;

abstract class Output {

    /** @var Formatter */
    private $formatter;

    public function __construct(Formatter $formatter) {

        $this->setFormatter($formatter);
    }

    /**
     * @return Formatter
     */
    public function getFormatter(): Formatter {
        return $this->formatter;
    }

    /**
     * @param Formatter $formatter
     */
    public function setFormatter(Formatter $formatter) {
        $this->formatter = $formatter;
    }

    /**
     * Writes a message to the output.
     *
     * @param string|iterable<string> $messages The message as an iterable of strings or a single string
     * @param bool            $newline  Whether to add a newline
     */
    public function write($messages, bool $newline = false) {
        if (is_string($messages)) $messages = [$messages];
        assert(is_iterable($messages));
        foreach ($messages as $message) {
            $message = $this->getFormatter()->format($message);
            $this->doWrite($message, $newline);
        }
    }

    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param string|iterable $messages The message as an iterable of strings or a single string
     */
    public function writeln(string $messages) {
        $this->write($messages, true);
    }

    /**
     * Writes a message to the output.
     */
    abstract protected function doWrite(string $message, bool $newline);
}

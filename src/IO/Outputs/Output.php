<?php

declare(strict_types=1);

namespace NGSOFT\Tools\IO\Outputs;

use NGSOFT\Tools\Interfaces\{
    FormatterInterface, OutputInterface
};

abstract class Output implements OutputInterface {

    /** FormatterInterface */
    protected $formatter;

    /** {@inheritdoc} */
    public function write($messages, $newline = false) {
        if (is_string($messages)) $messages = [$messages];
        assert(is_iterable($messages));

        foreach ($messages as $message) {
            if ($this->formatter !== null) $message = $this->formatter->format($message);
            $this->doWrite($message, $newline);
        }
    }

    /** {@inheritdoc} */
    public function writeln(string $messages) {
        $this->write($messages, true);
    }

    /** {@inheritdoc} */
    public function setFormatter(FormatterInterface $formatter) {
        $this->formatter = $formatter;
    }

    /**
     * Writes a message to the output.
     */
    abstract protected function doWrite(string $message, bool $newline);
}

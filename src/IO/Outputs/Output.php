<?php

declare(strict_types=1);

namespace NGSOFT\Tools\IO\Outputs;

use NGSOFT\Tools\Interfaces\{
    FormatterInterface, OutputInterface
};

abstract class Output implements OutputInterface {

    /** {@inheritdoc} */
    public function write($messages, $newline = false, FormatterInterface $formatter = null) {
        if (is_string($messages)) $messages = [$messages];
        assert(is_iterable($messages));

        foreach ($messages as $message) {
            if ($formatter !== null) $message = $formatter->format($message);
            $this->doWrite($message, $newline);
        }
    }

    /** {@inheritdoc} */
    public function writeln(string $messages, FormatterInterface $formatter = null) {
        $this->write($messages, true, $formatter);
    }

    /**
     * Writes a message to the output.
     */
    abstract protected function doWrite(string $message, bool $newline);
}

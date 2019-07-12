<?php

namespace NGSOFT\Tools\Interfaces;

interface OutputInterface {

    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param string|iterable<string> $messages
     * @param FormatterInterface|null $formatter
     */
    public function writeln(string $messages);

    /**
     * Writes a message to the output.
     *
     * @param string|iterable<string> $messages
     * @param bool $newline  Whether to add a newline
     * @param FormatterInterface|null $formatter
     */
    public function write($messages, $newline = false);

    /**
     * Set the Formatter
     * @param FormatterInterface $formatter
     */
    public function setFormatter(FormatterInterface $formatter);
}

<?php

namespace NGSOFT\Tools\Interfaces;

interface OutputInterface {

    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param string|iterable<string> $messages
     * @param FormatterInterface|null $formatter
     */
    public function writeln(string $messages, FormatterInterface $formatter = null);

    /**
     * Writes a message to the output.
     *
     * @param string|iterable<string> $messages
     * @param bool $newline  Whether to add a newline
     * @param FormatterInterface|null $formatter
     */
    public function write($messages, $newline = false, FormatterInterface $formatter = null);
}

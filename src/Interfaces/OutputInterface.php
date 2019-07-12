<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Interfaces;

interface OutputInterface {

    /**
     * Writes a message to the output and adds a newline at the end.
     *
     * @param string|iterable<string> $messages
     */
    public function writeln($messages);

    /**
     * Writes a message to the output.
     *
     * @param string|iterable<string> $messages
     * @param bool $newline  Whether to add a newline
     */
    public function write($messages, $newline = false);

    /**
     * Set the Formatter
     * @param FormatterInterface $formatter
     */
    public function setFormatter(FormatterInterface $formatter);
}

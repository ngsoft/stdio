<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Interfaces;

interface Output {

    /**
     * Writes a message to the output.
     *
     * @param string $message
     * @param bool $newline  Whether to add a newline
     */
    public function write(string $message);

    /**
     * Set the Formatter
     * @param Formatter $formatter
     */
    public function setFormatter(Formatter $formatter);

    /**
     * Get the Formatter
     * @return Formatter
     */
    public function getFormatter(): Formatter;

    /**
     * Get new instance using defined formatter
     * @param Formatter $formatter
     * @return static
     */
    public function withFormatter(Formatter $formatter);
}

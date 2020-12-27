<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Interfaces;

interface Buffer {

    /**
     * Adds Message to the buffer
     *
     * @param string $message
     */
    public function write(string $message);

    /**
     * Get Current Buffer
     * @return array<string>
     */
    public function getBuffer(): array;

    /**
     * Output and empties the Buffer
     * @param Output $output
     */
    public function flush(Output $output);

    /**
     * Clears the Buffer
     */
    public function clear();
}

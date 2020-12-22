<?php

namespace NGSOFT\STDIO\Interfaces;

interface Buffer {

    /**
     * Adds Message to the buffer
     *
     * @param string $message
     * @param bool $newline  Whether to add a newline
     */
    public function write(string $message, bool $newline = false);

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

<?php

namespace NGSOFT\STDIO\Interfaces;

interface Buffer {

    /**
     * Adds Message to the buffer
     *
     * @param string $message
     */
    public function write(string $message);

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

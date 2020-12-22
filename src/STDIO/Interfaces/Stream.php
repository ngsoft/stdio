<?php

namespace NGSOFT\STDIO\Interfaces;

interface Stream {

    /**
     * Get the stream
     * @return resource
     */
    public function getStream(): resource;

    /**
     * Gets a new instance with defined stream
     * @param resource $stream
     * @return static
     */
    public function withStream(resource $stream);
}

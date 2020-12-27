<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Interfaces;

interface Stream {

    /**
     * Get the stream
     * @return resource
     */
    public function getStream();

    /**
     * Gets a new instance with defined stream
     * @param resource $stream
     * @return static
     */
    public function withStream($stream);
}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Interfaces;

interface Stream {

    /**
     * Get the stream
     * @return resource
     */
    public function getStream();
}

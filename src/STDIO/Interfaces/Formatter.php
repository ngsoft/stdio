<?php

namespace NGSOFT\STDIO\Interfaces;

interface Formatter {

    /**
     * Formats a message.
     */
    public function format(string $message): string;
}

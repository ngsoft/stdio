<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Interfaces;

use NGSOFT\STDIO\{
    Styles, Terminal
};

interface Formatter {

    /**
     * Format a messsage
     * @param string $message
     * @return string
     */
    public function format(string $message): string;
}

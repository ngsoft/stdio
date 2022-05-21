<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use Stringable;

interface FormatterInterface {

    /**
     * Format a message
     *
     * @param string|Stringable|string[]|Stringable[] $message
     * @return string
     */
    public function format(string|Stringable|array $message): string;
}

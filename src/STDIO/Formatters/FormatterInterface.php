<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\STDIO\Enums\{
    Color, Format
};
use Stringable;

interface FormatterInterface {

    /**
     * Format a message
     *
     * @param string|Stringable|string[]|Stringable[] $messages
     * @return string
     */
    public function format(string|Stringable|array $messages): string;
}

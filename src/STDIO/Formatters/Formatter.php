<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

interface Formatter
{

    /**
     * Format the output
     */
    public function format(string|\Stringable $message): string;
}

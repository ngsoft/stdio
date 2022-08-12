<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

interface Formatter
{

    public function format(string|\Stringable $message): string;
}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

class NullFormatter implements Formatter
{

    public function format(string|\Stringable $message): string
    {
        return (string) $message;
    }

}

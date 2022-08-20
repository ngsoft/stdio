<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

use NGSOFT\STDIO\{
    Enums\Align, Formatters\Formatter
};
use Stringable;

class WordWrap extends Helper implements Formatter
{

    protected int $max;
    protected int $min;
    protected Align $align = Align::RIGHT;

    public function format(string|Stringable $message): string
    {

        $message = (string) $message;

        return $message;
    }

    public function __toString(): string
    {
        return $this->format($this->buffer);
    }

}

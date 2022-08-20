<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

class WordWrap extends Helper implements \NGSOFT\STDIO\Formatters\Formatter
{

    protected int $max;
    protected int $min;
    protected int $align;

    public function format(string|\Stringable $message): string
    {

        $message = (string) $message;

        return $message;
    }

    public function __toString(): string
    {
        return $this->format($this->buffer);
    }

}

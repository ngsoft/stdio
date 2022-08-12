<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\STDIO\Styles\Styles,
    Stringable;

class TagFormatter implements Formatter
{

    public function __construct(protected ?Styles $styles = null)
    {
        $this->styles ??= new Styles();
    }

    public function format(string|Stringable $message): string
    {

        return (string) $message;
    }

}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\STDIO\Styles\Styles,
    Stringable;

class TagFormatter implements Formatter
{

    protected array $formats = [
        'fg' => [],
        'bg' => [],
        'options' => []
    ];

    public function __construct(protected ?Styles $styles = null)
    {
        $this->styles ??= new Styles();
    }

    public function format(string|Stringable $message): string
    {

        return (string) $message;
    }

}

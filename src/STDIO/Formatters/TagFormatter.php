<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\STDIO\Styles\Styles,
    Stringable;

class TagFormatter implements Formatter
{

    protected array $formats = [];

    public function __construct(protected ?Styles $styles = null)
    {
        $this->styles ??= new Styles();
        $this->build();
    }

    protected function build(): void
    {
        $formats = &$this->formats;
    }

    public function format(string|Stringable $message): string
    {
        return (string) $message;
    }

}

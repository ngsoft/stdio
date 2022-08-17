<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

use NGSOFT\STDIO\{
    Outputs\Buffer, Outputs\Output, Outputs\Renderer, Styles\Style, Styles\StyleList
};
use Stringable;

abstract class Helper implements Renderer, Stringable
{

    protected Buffer $buffer;
    protected ?Style $style = null;

    public static function create(?StyleList $styles = null)
    {
        return new static($styles);
    }

    public function __construct(
            protected ?StyleList $styles = null
    )
    {
        $this->buffer = new Buffer();
        $this->styles ??= new StyleList();
    }

    public function getStyle(): Style
    {
        return $this->style ??= $this->styles->create('');
    }

    public function setStyle(Style $style): static
    {
        $this->style = $style;
        return $this;
    }

    /**
     * Add a line to the buffer
     */
    public function write(string|Stringable $message): static
    {
        $this->buffer->write($message);
        return $this;
    }

    /**
     * Format the message
     */
    abstract public function format(string|Stringable $message): string;

    public function render(Output $output): void
    {
        $output->write($this);
    }

    public function __toString(): string
    {
        return $this->format($this->buffer);
    }

}

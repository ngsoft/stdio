<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

use NGSOFT\STDIO\{
    Enums\BackgroundColor, Enums\Color, Formatters\Formatter, Outputs\Buffer, Outputs\Output, Outputs\Renderer, Styles\Style, Styles\Styles
};

/**
 * Draws Rectangles
 */
class Rect implements Renderer, Formatter, \Stringable
{

    protected const DEFAULT_STYLE = [
        'whiterect',
        Color::BLACK,
        BackgroundColor::GRAY
    ];

    protected Style $style;
    protected Buffer $buffer;
    protected string $text = '';
    protected int $padding = 4;

    public function __construct(
            protected ?Styles $styles = null
    )
    {
        $this->buffer = new Buffer();
        $this->styles ??= new Styles();
        $this->style = $this->styles->createStyle(...self::DEFAULT_STYLE);
    }

    public function getStyle(): Style
    {
        return $this->style;
    }

    public function setStyle(Style $style): static
    {
        $this->style = $style;
        return $this;
    }

    public function render(Output $output): void
    {
        $output->writeln($this);
    }

    public function format(string|\Stringable $message): string
    {
        if ($message instanceof self) {
            throw new InvalidArgumentException('$message cannot be instance of ' . __CLASS__);
        }
    }

    public function __toString(): string
    {
        return $this->format($this->text);
    }

}

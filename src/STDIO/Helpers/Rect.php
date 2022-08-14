<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

use InvalidArgumentException;
use NGSOFT\{
    Facades\Terminal, STDIO\Enums\BackgroundColor, STDIO\Enums\Color, STDIO\Formatters\Formatter, STDIO\Outputs\Buffer, STDIO\Outputs\Output, STDIO\Outputs\Renderer,
    STDIO\Styles\Style, STDIO\Styles\Styles
};
use Stringable;

/**
 * Draws Rectangles
 */
class Rect implements Renderer, Formatter, Stringable
{

    protected const DEFAULT_STYLE = [
        'white_rect',
        Color::BLACK,
        BackgroundColor::GRAY
    ];

    protected Style $style;
    protected Buffer $buffer;
    protected int $padding = 4;
    protected int $length;

    public function __construct(
            protected ?Styles $styles = null
    )
    {
        $this->buffer = new Buffer();
        $this->styles ??= new Styles();
        $this->style = $this->styles->createStyle(...self::DEFAULT_STYLE);
        $this->length = Terminal::getWidth();
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function setLength(int $length)
    {
        $this->length = min(max(1, $length), Terminal::getWidth());
        return $this;
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

    public function format(string|Stringable $message): string
    {
        if ($message instanceof self) {
            throw new InvalidArgumentException('$message cannot be instance of ' . __CLASS__);
        }

        if (empty($message)) {
            return '';
        }
    }

    public function __toString(): string
    {
        return $this->format(implode("\n", $this->buffer->pull()));
    }

}

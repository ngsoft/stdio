<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

use InvalidArgumentException;
use NGSOFT\{
    Facades\Terminal, STDIO\Enums\Ansi, STDIO\Enums\BackgroundColor, STDIO\Enums\Color, STDIO\Formatters\Formatter, STDIO\Outputs\Buffer, STDIO\Outputs\Output,
    STDIO\Outputs\Renderer, STDIO\Styles\Style, STDIO\Styles\Styles
};
use RuntimeException,
    Stringable;
use function NGSOFT\Tools\split_string;

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
    protected int $margin = 2;
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

    public function getPadding(): int
    {
        return $this->padding;
    }

    public function getMargin(): int
    {
        return $this->margin;
    }

    /**
     * Set Rectangle inner padding
     */
    public function setPadding(int $padding)
    {
        $this->padding = max(0, $padding);
        return $this;
    }

    /**
     * Set rectangle margin
     */
    public function setMargin(int $margin)
    {
        $this->margin = max(0, $margin);
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
        $output->write($this);
    }

    /**
     * Add a line to the rectangle
     */
    public function write(string|Stringable $message): void
    {
        $this->buffer->write($message);
    }

    public function format(string|Stringable $message): string
    {
        if ($message instanceof self) {
            throw new InvalidArgumentException('$message cannot be instance of ' . __CLASS__);
        }

        $message = (string) $message;

        if (empty($message)) {
            return '';
        }

        $colors = $this->styles->colors;
        $style = $this->style;

        $result = [];

        $pad = $this->padding > 0 ? str_repeat(' ', $this->padding) : '';

        $margin = $this->margin > 0 ? str_repeat(' ', $this->margin) : '';

        $maxLength = Terminal::getWidth() - ($this->margin * 2);

        $length = $this->length;

        if ($maxLength > $this->length) {
            $length = $maxLength;
        }

        $length -= $this->margin * 2;

        $header = $margin . $style->format(str_repeat(' ', $length), $colors) . $margin;

        $length -= $this->padding * 2;

        $lineLength = $length;

        $result[] = "\n{$header}\n";

        foreach (preg_split('#[\n\r]+#', $message) as $messageLine) {
            $lines = split_string($messageLine, $lineLength);

            if ($lineLength > $length) {
                throw new RuntimeException(sprintf('Cannot render %s message, a word size %d is greater than the output line size %d.', __CLASS__, $lineLength, $length));
            }



            foreach ($lines as $line) {
                $result[] = $margin;

                $padLength = max(0, $length - mb_strlen($line));

                $padLength /= 2;
                $padLeft = (int) ceil($padLength);
                $padRight = (int) floor($padLength);

                $contents = sprintf(
                        '%s%s%s',
                        $padLeft ? str_repeat(' ', $padLeft) : '',
                        $line,
                        $padRight ? str_repeat(' ', $padRight) : ''
                );

                $result[] = $style->format($pad . $contents . $pad, $colors);
                $result[] = "{$margin}\n";
            }
        }


        $result[] = "{$header}\n\n";

        $result = implode('', $result);
        if ($colors) {
            $result = Ansi::RESET . $result . Ansi::RESET;
        }
        return $result;
    }

    public function __toString(): string
    {
        return $this->format(implode("\n", $this->buffer->pull()));
    }

}

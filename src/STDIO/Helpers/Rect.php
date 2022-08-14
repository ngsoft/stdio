<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

use InvalidArgumentException;
use NGSOFT\{
    Facades\Terminal, STDIO, STDIO\Enums\Ansi, STDIO\Enums\BackgroundColor, STDIO\Enums\Color, STDIO\Formatters\Formatter, STDIO\Outputs\Buffer, STDIO\Outputs\Output,
    STDIO\Outputs\Renderer, STDIO\Styles\Style, STDIO\Styles\Styles
};
use RuntimeException,
    Stringable;
use function mb_strlen;
use function NGSOFT\Tools\{
    split_string, str_word_size
};

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
    protected int $length = 0;
    protected bool $center = false;

    public function create(?Styles $styles = null)
    {
        return new static($styles ?? STDIO::getCurrentInstance()->getStyles());
    }

    public function __construct(
            protected ?Styles $styles = null
    )
    {
        $this->buffer = new Buffer();
        $this->styles ??= new Styles();
        $this->style = $this->styles->createStyle(...self::DEFAULT_STYLE);
    }

    public function getCenter(): bool
    {
        return $this->center;
    }

    /**
     * Centers rectangle to the output
     */
    public function setCenter(bool $center = true)
    {
        $this->center = $center;
        return $this;
    }

    /**
     * Set length to max
     */
    public function autoSetLength(): static
    {
        $this->setLength(Terminal::getWidth());
        return $this;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function setLength(int $length): static
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
    public function setPadding(int $padding): static
    {
        $this->padding = max(0, $padding);
        return $this;
    }

    /**
     * Set rectangle margin
     * even values in range [ 0 - 10 ]
     */
    public function setMargin(int $margin): static
    {
        if ($margin % 2 === 1) {
            $margin --;
        }

        $this->margin = max(0, min($margin, 10));
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
    public function write(string|Stringable $message): static
    {
        $this->buffer->write($message);
        return $this;
    }

    /** {@inheritdoc} */
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

        $maxLength = Terminal::getWidth();

        // max margin 10%
        $maxMargin = (int) floor($maxLength / 10);

        if ($maxMargin % 2 === 1) {
            $maxMargin --;
        }

        $margin = max(0, min($maxMargin, $this->margin, 10));
        $maxLength -= $margin * 2;

        $minLength = str_word_size($message);

        if ($minLength === 0) {
            throw new RuntimeException('Cannot render message that have no words.');
        }

        $maxPad = max((int) floor(($maxLength - $minLength) / 2), 0);

        $padding = min($maxPad, $this->padding);

        $length = $this->length;

        if ($length === 0) {
            $length = mb_strlen(preg_split('#\v+#', $message)[0]) + ($padding * 2);
        }

        $length = max(min($length, $maxLength), $minLength + ($padding * 2));

        $center = '';

        if ($this->center) {
            $diff = (int) ceil(($maxLength - $length) / 2);
            $diff > 0 && $center = str_repeat(' ', $diff);
        }


        $margin = $margin > 0 ? str_repeat(' ', $margin) : '';

        $header = $margin . $style->format(str_repeat(' ', $length), $colors) . $margin;

        $length -= $padding * 2;

        $pad = $padding > 0 ? str_repeat(' ', $padding) : '';

        $lineLength = $length;

        $result = [
            "\n\n",
            $center,
            $header,
            "\n"
        ];

        foreach (preg_split('#\v+#', $message) as $messageLine) {
            $lines = split_string($messageLine, $lineLength);

            foreach ($lines as $line) {

                $result[] = $margin;
                $result[] = $center;

                $padLength = max(0, $length - mb_strlen($line));

                $padLength /= 2;
                $padLeft = (int) floor($padLength);
                $padRight = (int) ceil($padLength);

                $contents = sprintf(
                        '%s%s%s',
                        $padLeft ? str_repeat(' ', $padLeft) : '',
                        $line,
                        $padRight ? str_repeat(' ', $padRight) : ''
                );

                $result[] = $style->format($pad . $contents . $pad, $colors);
                $result[] = $margin;
                $result[] = "\n";
            }
        }


        $result[] = $center;
        $result[] = $header;
        $result[] = "\n\n";

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

    public function __debugInfo(): array
    {
        return [
            'length' => $this->length,
            'padding' => $this->padding,
            'margin' => $this->margin,
            'center' => $this->center,
            'style' => $this->style->format($this->style->getLabel(), $this->styles->colors),
        ];
    }

}

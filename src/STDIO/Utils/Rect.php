<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils;

use NGSOFT\{
    STDIO, STDIO\Interfaces\Output, STDIO\Interfaces\Renderer, STDIO\Outputs\OutputBuffer, STDIO\Styles, STDIO\Styles\Style, STDIO\Terminal
};
use function mb_strlen;

class Rect implements Renderer {

    /** @var Terminal */
    protected $term;

    /** @var Styles */
    protected $styles;

    /** @var OutputBuffer */
    protected $buffer;

    /** @var Style */
    protected $color;

    /** @var Style */
    protected $background;

    /** @var Style */
    protected $emptyStyle;

    /** @var STDIO */
    protected $stdio;

    /** @var int */
    protected $length;

    /** @var int */
    protected $marginLeft = 2;

    public function __construct(
            STDIO $stdio = null
    ) {

        $stdio = $stdio ?? STDIO::create();
        $this->emptyStyle = $this->background = $this->color = new Style(false);
        $this->stdio = $stdio;
        $this->term = $stdio->getTerminal();
        $this->styles = $stdio->getStyles();
        $this->buffer = new OutputBuffer();

        $this->setLength(56);
    }

    /**
     * Set Text Color
     * @param string|int $color
     * @return static
     */
    public function setColor($color): self {
        $this->color = $this->styles[$color] ?? $this->emptyStyle;
        return $this;
    }

    /**
     * Set Background Color
     * @param string|int $color
     * @return static
     */
    public function setBackground($color): self {
        if (is_int($color) && $style = $this->styles[$color]) $color = $style->getName();
        if (is_string($color) && strpos($color, 'bg') !== 0) $color = "bg$color";
        $this->background = $this->styles[$color] ?? $this->emptyStyle;
        return $this;
    }

    /**
     * Adds a line to the Rectangle
     * @param string $line
     * @return static
     */
    public function write(string $line): self {
        $this->buffer->write(trim($line));
        return $this;
    }

    /**
     * Set left margin
     * @param int $marginLeft
     * @return $this
     */
    public function setMarginLeft(int $marginLeft) {
        $this->marginLeft = $marginLeft;
        return $this;
    }

    /**
     * Set Rectange line length (with padding)
     *
     * @param int $maxLength
     * @return self
     */
    public function setLength(int $maxLength): self {
        $this->length = min($maxLength, $this->term->width);
        return $this;
    }

    /**
     * Cut string at length, also add spaces to get the right length
     *
     * @param string $string
     * @param int $lineLength
     * @return array
     */
    protected function cutString(string $string, int $lineLength): array {
        $result = [];

        // -2 to get a padding before and after the line
        $length = $lineLength - 2;

        if (mb_strlen($string) > $length) {
            //cut words at tab, spaces ...
            $words = preg_split('/\h+/', $string);

            $line = '';
            foreach ($words as $index => $word) {
                if (mb_strlen($line . " $word") > $length) {
                    $result[] = $line;
                    $line = '';
                }
                $line .= !empty($line) ? " $word" : $word;
                if (!array_key_exists($index + 1, $words)) {
                    $result[] = $line;
                }
            }
        } else $result[] = $string;

        // center the lines
        foreach ($result as $index => $line) {
            $len = mb_strlen($line);
            if ($len < $lineLength) {
                $repeatLeft = max(0, (int) ceil(($lineLength - $len) / 2));
                $repeatRight = max(0, $lineLength - $len - $repeatLeft);
                for ($i = 0; $i < $repeatLeft; $i++) {
                    $line = " $line";
                }
                for ($i = 0; $i < $repeatRight; $i++) {
                    $line = "$line ";
                }
            }

            $result[$index] = $line;
        }


        return $result;
    }

    /**
     * Create the string to be rendered
     *
     * @return string
     */
    protected function build(): string {
        if (count($this->buffer) == 0) return '';
        $lines = [];

        $clear = $this->styles->reset->getSuffix();
        $length = $this->length;

        $header = $this->cutString('', $length)[0];

        $rect = [$header];
        foreach ($this->buffer as $bufferLine) {
            foreach ($this->cutString($bufferLine, $length) as $l) {
                $rect[] = $l;
            }
        }
        $rect[] = $header;
        $available = $this->term->width - $length;
        if ($this->marginLeft > 0) $margin = $available > $this->marginLeft ? $this->marginLeft : max(0, $available - 2);
        else $margin = 0;

        foreach ($rect as $line) {
            // removes all styles
            $message = $clear;
            //margin
            for ($i = 0; $i < $margin; $i++) {
                $message .= ' ';
            }
            // add styles to line
            $message .= $this->color->format($this->background->format($line));
            $lines[] = $message;
        }

        if (!empty($lines)) $lines[] = '';

        return implode("\n", $lines) . "\n";
    }

    /** {@inheritdoc} */
    public function render(Output $output) {
        if (count($this->buffer) > 0) {
            $text = $this->build();
            $output->write($text);
            $this->buffer->clear();
        }
    }

    /**
     * @param string $message
     */
    protected function bufferMessage(string $message) {
        $message = explode("\n", $message);
        foreach ($message as $line) {
            $this->write($line);
        }
    }

    /**
     * Render Rect into StdOUT
     *
     * @param ?string $message
     * @return static
     */
    public function out(string $message = null): self {
        if (is_string($message)) {
            $this->buffer->clear();
            $this->bufferMessage($message);
        }
        if (count($this->buffer) > 0) $this->render($this->stdio->getOutput());
        return $this;
    }

    /**
     * Render Rect into StdERR
     *
     * @param ?string $message
     * @return static
     */
    public function err(string $message = null) {
        if (is_string($message)) {
            $this->buffer->clear();
            $this->bufferMessage($message);
        }
        if (count($this->buffer) > 0) $this->render($this->stdio->getErrorOutput());
        return $this;
    }

}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils;

use NGSOFT\{
    STDIO, STDIO\Interfaces\Buffer, STDIO\Interfaces\Output, STDIO\Interfaces\Renderer, STDIO\Outputs\OutputBuffer, STDIO\Styles, STDIO\Styles\Style, STDIO\Terminal
};
use function mb_strlen;

class Rect implements Renderer {

    const DEFAULT_OUTPUT = 'out';

    /** @var Terminal */
    protected $term;

    /** @var Styles */
    protected $styles;

    /** @var Buffer */
    protected $buffer;

    /** @var Style */
    protected $color;

    /** @var Style */
    protected $background;

    /** @var int */
    protected $padding = 2;

    /** @var STDIO */
    protected $stdio;

    /** @var int */
    protected $maxLength;

    /** @var int */
    protected $minLength;

    /** @var int */
    protected $marginLeft;

    public function __construct(
            STDIO $stdio = null
    ) {
        $stdio = $stdio ?? STDIO::create();
        $this->stdio = $stdio;
        $this->term = $stdio->getTerminal();
        $this->styles = $stdio->getStyles();
        $this->buffer = new OutputBuffer();
        $this->setMinLength(0);
        $this->setMaxLength(64);
        $this->setMarginLeft(2);
    }

    /**
     * Set Text Color
     * @param string $color
     * @return static
     */
    public function setColor(string $color): self {
        if (isset($this->styles[$color])) $this->color = $this->styles[$color];
        return $this;
    }

    /**
     * Set Background Color
     * @param string $color
     * @return static
     */
    public function setBackground(string $color): self {
        if (isset($this->styles["bg$color"])) $this->background = $this->styles["bg$color"];
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
     * Set Styles
     * @param Styles $styles
     * @return static
     */
    public function setStyles(Styles $styles): self {
        $this->styles = $styles;
        return $this;
    }

    /**
     * Set Line Padding
     *
     * @param int $padding
     * @return static
     */
    public function setPadding(int $padding): self {
        $this->padding = $padding;
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
     * Set Line Max Length
     *
     * @param int $maxLength
     * @return self
     */
    public function setMaxLength(int $maxLength): self {
        $this->maxLength = min($maxLength, $this->term->width);
        return $this;
    }

    /**
     * Set Line Min Length
     *
     * @param int $minLength
     * @return $this
     */
    public function setMinLength(int $minLength) {
        $this->minLength = min($minLength, $this->term->width);
        return $this;
    }

    /**
     * Create the string to be rendered
     *
     * @return string
     */
    protected function build(): string {

        if (count($this->buffer) == 0) return '';

        $result = [''];

        $prefix = $suffix = $clear = '';
        if ($this->term->hasColorSupport()) {
            $clear = $this->styles->reset->getSuffix();
            if ($this->color instanceof Style) {
                $prefix .= $this->color->getPrefix();
                $suffix .= $this->color->getSuffix();
            }

            if ($this->background instanceof Style) {
                $prefix .= $this->background->getPrefix();
                $suffix .= $this->background->getSuffix();
            }
        }

        $max = 0;

        $rect = [''];
        foreach ($this->buffer as $line) {
            $len = mb_strlen($line);
            $max = $len > $max ? $len : $max;
            $rect[] = $line;
        }
        $rect[] = '';

        $maxlen = min($this->maxLength, $max);
        if ($this->minLength > 0) $maxlen = max($maxlen, $this->minLength);

        $available = $this->term->width - $maxlen;
        //padding
        $padding = 0;
        if ($available > ($this->padding * 2)) {
            $padding = $this->padding;
            $available -= $padding * 2;
        }
        //margin
        $marginLeft = 0;
        if ($available > $this->marginLeft) $marginLeft = $this->marginLeft;

        foreach ($rect as $line) {
            $message = $clear;
            if ($marginLeft > 0) $message .= str_repeat(' ', $marginLeft);
            $len = mb_strlen($line);
            $repeatLeft = (int) ceil(($maxlen - $len) / 2);
            $repeatRight = $maxlen - $len - $repeatLeft;
            $message .= $prefix;
            if ($padding > 0) $message .= str_repeat(' ', $padding);
            if ($repeatLeft > 0) $message .= str_repeat(' ', $repeatLeft);
            $message .= $line;
            if ($repeatRight > 0) $message .= str_repeat(' ', $repeatLeft);
            if ($padding > 0) $message .= str_repeat(' ', $padding);
            $message .= $suffix;
            $result[] = $message;
        }
        return implode("\n", $result) . "\n";
    }

    /** {@inheritdoc} */
    public function render(Output $output) {
        $text = $this->build();
        $output->write($text);
        $output->write("\n");
        $this->buffer->clear();
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
        if (is_string($message)) $this->bufferMessage($message);
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
        if (is_string($message)) $this->bufferMessage($message);
        if (count($this->buffer) > 0) $this->render($this->stdio->getErrorOutput());
        return $this;
    }

}

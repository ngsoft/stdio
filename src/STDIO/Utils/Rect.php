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

    public function __construct(
            STDIO $stdio = null
    ) {
        $this->stdio = $stdio ?? new STDIO();
        $this->term = $stdio->getTerminal();
        $this->styles = $stdio->getStyles();
        $this->buffer = new OutputBuffer();
        $this->setMaxLength(64);
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
     * Create the string to be rendered
     *
     * @return string
     */
    protected function build(): string {
        $result = [''];
        $lines = $this->buffer->getBuffer();

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










        return implode("\n", $result) . "\n";
    }

    protected function old_build(): string {
        $result = [""];
        $lines = $this->buffer->getBuffer();

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

        $maxlen = 64;

        $rectlines = [""];
        foreach ($lines as $line) {
            if (mb_strlen($line) > $maxlen) $maxlen = mb_strlen($line) + 8;
            $rectlines[] = $line;
        }

        $rectlines[] = "";

        $margin_left = (int) floor(($this->term->width - $maxlen) / 2);

        foreach ($rectlines as $line) {
            $message = $clear;
            $message .= $margin_left > 0 ? str_repeat(" ", $margin_left) : '';
            $len = mb_strlen($line);
            $repeatsl = (int) ceil(($maxlen - $len) / 2);
            $repeatsr = $maxlen - $len - $repeatsl;
            $message .= $prefix;
            $message .= $repeatsl > 0 ? str_repeat(" ", $repeatsl) : '';
            $message .= $line;
            $message .= $repeatsr > 0 ? str_repeat(" ", $repeatsr) : '';
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
     * @return static
     */
    protected function bufferMessage(string $message): self {

        $message = explode("\n", $message);
        foreach ($message as $line) {
            $this->write($line);
        }
    }

    public function out(string $message = null): self {
        if (is_string($message)) $this->bufferMessage($message);
        $this->render($this->stdio->getSTDOUT());
        return $this;
    }

    public function err(string $message = null) {
        if (is_string($message)) $this->bufferMessage($message);
        $this->render($this->stdio->getSTDERR());
        return $this;
    }

}

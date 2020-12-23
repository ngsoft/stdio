<?php

namespace NGSOFT\STDIO\Utils;

use NGSOFT\STDIO\{
    Interfaces\Ansi, Interfaces\Buffer, Interfaces\Output, Interfaces\Renderer, Outputs\OutputBuffer, Styles, Styles\Style, Terminal
};
use function mb_strlen;

class Rect implements Renderer {

    /** @var Terminal */
    private $term;

    /** @var Styles */
    private $styles;

    /** @var Buffer */
    private $buffer;

    /** @var Style */
    private $color;

    /** @var Style */
    private $background;

    public function __construct() {
        $this->term = new Terminal();
        $this->buffer = new OutputBuffer();
        $this->styles = new Styles();
    }

    /**
     * Set Text Color
     * @param string $color
     * @return static
     */
    public function setColor(string $color) {
        if (isset($this->styles[$color])) $this->color = $this->styles[$color];
        return $this;
    }

    /**
     * Set Background Color
     * @param string $color
     * @return static
     */
    public function setBackground(string $color) {
        if (isset($this->styles["bg$color"])) $this->background = $this->styles["bg$color"];
        return $this;
    }

    /**
     * Adds a line to the Rectangle
     * @param string $line
     * @return $this
     */
    public function write(string $line) {
        $this->buffer->write(trim($line));
        return $this;
    }

    /** {@inheritdoc} */
    public function setStyles(Styles $styles) {
        $this->styles = $styles;
    }

    private function build(): string {
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
            if (mb_strlen($line) > $maxlen) $maxlen = mb_strlen($line);
            $rectlines[] = $line;
        }

        $rectlines[] = "";

        $margin_left = (int) floor(($this->term->width - $maxlen) / 2);
        foreach ($rectlines as $line) {
            $message = $clear . str_repeat(" ", $margin_left);
            $len = mb_strlen($line);
            $repeatsl = (int) ceil(($maxlen - $len) / 2);
            $repeatsr = $maxlen - $len - $repeatsl;
            $message .= $prefix . str_repeat(" ", $repeatsl) . $line . str_repeat(" ", $repeatsr);
            $message .= $suffix;
            $result[] = $message;
        }
        return implode("\n", $result);
    }

    /** {@inheritdoc} */
    public function render(Output $output) {

        $text = $this->build();
        $output->write($text);
        $this->buffer->clear();
    }

}

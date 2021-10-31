<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils;

use Generator,
    IteratorAggregate;
use NGSOFT\{
    STDIO, STDIO\Interfaces\Ansi, STDIO\Interfaces\Output, STDIO\Interfaces\Renderer, STDIO\Utils\Progress\Element, STDIO\Utils\Progress\Elements\Bar,
    STDIO\Utils\Progress\Elements\Percentage, STDIO\Utils\Progress\Elements\Status, STDIO\Utils\Progress\ProgressElement
};

class Progress implements Renderer, IteratorAggregate {

    /** @var STDIO */
    protected $stdio;

    /** @var Status */
    protected $status;

    /** @var Bar */
    protected $bar;

    /** @var Percentage */
    protected $percentage;

    /** @var Element */
    protected $label;

    /** @var ProgressElement[] */
    protected $elements;

    /** @var int */
    protected $total;

    /** @var int */
    protected $current = 0;

    /** @var bool */
    protected $alignRight = false;

    /** @var callable[] */
    protected $onComplete = [];

    /** @var bool */
    protected $complete = false;

    ////////////////////////////   Getter/Setter   ////////////////////////////

    /**
     * @param int $total
     * @param ?STDIO $stdio
     */
    public function __construct(
            int $total = 100,
            STDIO $stdio = null
    ) {
        $stdio = $stdio ?? new STDIO();
        $this->stdio = $stdio;
        $this->elements = [
            $this->status = new Status($total, $stdio),
            $this->bar = new Bar($total, $stdio),
            $this->percentage = new Percentage($total, $stdio),
        ];
        $this->setLabel('');
        $this->setTotal($total);
    }

    /**
     * @return Generator<ProgressElement>
     */
    public function getElements(): Generator {
        foreach ($this->elements as $element) yield $element;
    }

    /** @return int */
    public function getTotal(): int {
        return $this->total;
    }

    /** @return int */
    public function getCurrent(): int {
        return $this->current;
    }

    /**
     *
     * @param bool $alignRight
     * @return static
     */
    public function setAlignRight(bool $alignRight = true): self {
        $this->alignRight = $alignRight;
        return $this;
    }

    /**
     * @param int $total
     * @return static
     */
    public function setTotal(int $total): self {
        $this->current = 0;
        $total = max(1, $total);
        $this->total = $total;
        /** @var ProgressElement $element */
        foreach ($this->getElements() as $element) {
            $element->setCurrent(0);
            $element->setTotal($total);
        }
        return $this;
    }

    /**
     * @param int $current
     * @return static
     */
    public function setCurrent(int $current): self {
        $current = max(0, min($current, $this->total));
        $this->current = $current;
        /** @var ProgressElement $element */
        foreach ($this->getElements() as $element) {
            $element->setCurrent($current);
        }
        return $this;
    }

    /**
     * Checks if complete
     *
     * @return bool
     */
    public function getComplete(): bool {
        return $this->complete = $this->current == $this->total;
    }

    /**
     * Get Percentage Done
     * @return int
     */
    public function getPercent(): int {
        $percent = (int) floor(($this->current / $this->total) * 100);
        if ($percent > 100) $percent = 100;
        return $percent;
    }

    /**
     * Set Status color
     * @param string $color
     * @return static
     */
    public function setStatusColor(string $color): self {
        $this->status->setColor($color);
        return $this;
    }

    /**
     * Set Progress Bar Color
     * @param string $color
     * @return self
     */
    public function setBarColor(string $color): self {
        $this->bar->setColor($color);
        return $this;
    }

    /**
     * Set Percentage Color
     * @param string $color
     * @return self
     */
    public function setPercentageColor(string $color): self {
        $this->percentage->setColor($color);
        return $this;
    }

    /**
     * Set Label
     * @param string $label
     * @param ?string $color
     * @return static
     */
    public function setLabel(string $label, string $color = null): self {
        $element = new Element($this->stdio);
        if (
                is_string($color) and
                ( $style = $this->stdio->getStyles()[$color] ?? null)
        ) {
            $element->setStyle($style);
        }
        $element->setValue($label);
        $this->label = $element;
        return $this;
    }

    ////////////////////////////   Utils   ////////////////////////////

    /**
     * Hide Status
     * @return self
     */
    public function hideStatus(): self {
        $this->status->setVisible(false);
        return $this;
    }

    /**
     * Hide Progress Bar
     * @return self
     */
    public function hideBar(): self {
        $this->bar->setVisible(false);
        return $this;
    }

    /**
     * Hide Percentage
     * @return self
     */
    public function hidePercentage(): self {
        $this->percentage->setVisible(false);
        return $this;
    }

    /**
     * Increments the counter
     * @param int $value value to add
     * @return static
     */
    public function increment(int $value = 1) {
        $current = $this->current;
        $current += $value;
        $this->setCurrent($current);
        return $this;
    }

    /**
     * Decrements the Counter
     * @param int $value
     * @return $this
     */
    public function decrement(int $value = 1) {
        $current = $this->current;
        $current -= $value;
        $this->setCurrent($current);
        return $this;
    }

    protected function build(): string {
        $str = $block = '';
        $len = 0;

        if ($this->complete) return $str;

        if (count($this->label) > 0) {
            $block .= (string) $this->label . ' ';
            $len += count($this->label) + 1;
        }

        /** @var ProgressElement $element */
        foreach ($this->getElements() as $element) {
            $len += count($element);
            $block .= (string) $element;
        }

        $str .= sprintf("\r%s", Ansi::CLEAR_END_LINE);

        if ($this->alignRight) {
            $padding = $this->stdio->getTerminal()->width - 1;
            $padding -= $len;
            if ($padding > 0) $str .= str_repeat(' ', $padding);
        }

        $str .= $block;

        if ($this->getComplete()) {
            $str .= "\n";
        }

        return $str;
    }

    /**
     * Adds a callback to be called on completion
     * @param callable $callback
     * @return self
     */
    public function onComplete(callable $callback): self {
        $this->onComplete[] = $callback;
        return $this;
    }

    /**
     * Render into StdOUT
     *
     * @return static
     */
    public function out(): self {
        $this->render($this->stdio->getOutput());
        return $this;
    }

    /**
     * Render Rect into StdERR
     *
     * @return static
     */
    public function err() {
        $this->render($this->stdio->getErrorOutput());
        return $this;
    }

    ////////////////////////////   Interfaces   ////////////////////////////

    /** {@inheritdoc} */
    public function render(Output $output) {
        $output->write($this->build());

        if ($this->complete) {
            foreach ($this->onComplete as $call) {
                $call($this);
            }
        }
    }

    /**
     * @return Generator<int,int>
     */
    public function getIterator() {
        $this->setTotal($this->total);
        for ($i = 0; $i <= $this->total; $i++) {
            $this->setCurrent($i);
            $this->out();
            yield $this->total => $this->current;
        }
    }

}

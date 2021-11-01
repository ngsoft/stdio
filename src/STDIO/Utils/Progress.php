<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils;

use Generator,
    IteratorAggregate;
use NGSOFT\{
    STDIO, STDIO\Interfaces\Ansi, STDIO\Interfaces\Output, STDIO\Interfaces\Renderer, STDIO\Outputs\OutputBuffer, STDIO\Outputs\StreamOutput, STDIO\Styles, STDIO\Terminal,
    STDIO\Utils\Progress\Element, STDIO\Utils\Progress\Elements\Bar, STDIO\Utils\Progress\Elements\Percentage, STDIO\Utils\Progress\Elements\Status,
    STDIO\Utils\Progress\ProgressElement
};

class Progress implements Renderer, IteratorAggregate {

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
    protected $callbacks = [];

    /** @var bool */
    protected $complete = false;

    /** @var OutputBuffer */
    protected $buffer;

    /** @var Output */
    protected $output;

    /** @var Styles */
    protected $styles;

    /** @var bool */
    protected $rendered = false;

    ////////////////////////////   Getter/Setter   ////////////////////////////

    /**
     * Creates a new instance
     *
     * @param int $total
     * @param ?STDIO $stdio
     * @return static
     */
    public static function create(int $total = 100, STDIO $stdio = null): self {
        return new static($total, $stdio);
    }

    /**
     * @param int $total
     * @param ?STDIO $stdio
     */
    public function __construct(int $total = 100, STDIO $stdio = null) {
        $stdio = $stdio ?? STDIO::create();
        $this->output = new StreamOutput();
        $this->buffer = new OutputBuffer();
        $styles = $this->styles = $stdio->getStyles();
        $this->elements = [
            $this->status = new Status($total, $styles),
            $this->bar = new Bar($total, $styles),
            $this->percentage = new Percentage($total, $styles),
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
        $total = max(1, $total);
        $this->total = $total;
        /** @var ProgressElement $element */
        foreach ($this->getElements() as $element) {
            $element->setTotal($total);
        }
        $this->setCurrent(0);
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
        if ($this->getComplete()) $this->triggerComplete();
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
     * @param string|int|null $color
     * @return static
     */
    public function setLabel(string $label, $color = null): self {
        $element = new Element();
        if (
                is_string($color) and ($style = $this->styles[$color] ?? null)
        ) {
            $element->setStyle($style);
        }
        $element->setValue($label);
        $this->label = $element;
        return $this;
    }

    ////////////////////////////   Utils   ////////////////////////////

    /**
     * Run the callbacks once
     */
    protected function triggerComplete() {
        if ($this->complete) {
            $total = $this->total;
            while (null !== ($callable = array_shift($this->callbacks))) {
                call_user_func_array($callable, [$total]);
            }
        }
    }

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
        $value = max(1, $value);
        $current = min($this->current, $this->total - $value);
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
        $value = max(1, $value);
        $current = min($this->current, $this->total);
        $current -= $value;
        $this->setCurrent($current);
        return $this;
    }

    protected function build() {

        static $term;
        $term = $term ?? Terminal::create();

        if (count($this->buffer) == 0) {
            $str = $block = '';
            $len = 0;

            if (count($this->label) > 0) {
                $block .= (string) $this->label . ' ';
                $len += count($this->label) + 1;
            }

            /** @var ProgressElement $element */
            foreach ($this->getElements() as $element) {
                $len += count($element);
                $block .= (string) $element;
            }

            $str .= sprintf("\r%s", Ansi::CLEAR_LINE);

            if ($this->alignRight) {
                $padding = $term->width - 1;
                $padding -= $len;
                if ($padding > 0) $str .= str_repeat(' ', $padding);
            }
            $str .= $block;
            $this->buffer->write($str);
        }
    }

    /**
     * Adds a callback to be called on completion
     * @param callable $callback
     * @return self
     */
    public function onComplete(callable $callback): self {
        if ($this->complete) $callback($this->total);
        else $this->callbacks[] = $callback;
        return $this;
    }

    /**
     * Render into STDOUT
     *
     * @return static
     */
    public function out(): self {
        $this->render($this->output);
        return $this;
    }

    /**
     * Reset progress to 0 and set to erase the line
     * @return static
     */
    public function reset(): self {
        $this->setCurrent(0);
        if ($this->rendered) $this->buffer->write("\r" . Ansi::CLEAR_LINE);
        $this->buffer->flush($this->output);
        $this->rendered = false;
        return $this;
    }

    /**
     * Set the progress to complete and end the line
     *
     * @return static
     */
    public function end(): self {
        $this->setCurrent($this->total);
        if ($this->rendered) $this->buffer->write('\n');
        $this->buffer->flush($this->output);
        $this->rendered = false;
        return $this;
    }

    ////////////////////////////   Interfaces   ////////////////////////////

    /** {@inheritdoc} */
    public function render(Output $output) {
        $this->build();
        $this->buffer->flush($output);
        $this->rendered = true;
    }

    /**
     * Increments the progress as steps
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

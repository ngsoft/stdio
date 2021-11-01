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
use function mb_strlen;

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
        $this->label = new Element();
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
     * Displays bar to the right
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
        $this->complete = false;
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
        $this->buffer->clear();
        $this->build();
        if ($this->getComplete()) $this->triggerComplete();
        return $this;
    }

    /**
     * Checks if complete
     *
     * @return bool
     */
    public function getComplete(): bool {
        return $this->current == $this->total;
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
     * @param string|int $color
     * @return static
     */
    public function setStatusColor($color): self {
        $this->status->setColor($color);
        return $this;
    }

    /**
     * Set Progress Bar Color
     * @param string|int $color
     * @return static
     */
    public function setBarColor($color): self {
        $this->bar->setColor($color);
        return $this;
    }

    /**
     * Set Percentage Color
     * @param string|int $color
     * @return static
     */
    public function setPercentageColor($color): self {
        $this->percentage->setColor($color);
        return $this;
    }

    /**
     * Set Percentage Color
     * @param string|int $color
     * @return static
     */
    public function setLabelColor($color): self {
        if ($style = $this->styles[$color]) {
            $this->label->setStyle($style);
        }
        return $this;
    }

    /**
     * Set Label
     * @param string $label
     * @param ?int $length Length to reserve to prevent padding
     * @return static
     */
    public function setLabel(string $label, int $length = null): self {
        $length = $length ?? mb_strlen($label);
        // to prevent bar padding when changing label
        if (!empty($label) and mb_strlen($label) < $length) {
            while (mb_strlen($label) < $length) {
                $label .= ' ';
            }
        }
        $this->label->setValue($label);
        return $this;
    }

    ////////////////////////////   Utils   ////////////////////////////

    /**
     * Run the callbacks once
     */
    protected function triggerComplete() {
        if (!$this->complete && $this->getComplete()) {
            $this->complete = true;
            $total = $this->total;
            $this->current = 0;
            $this->buffer->write("\n");
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

        if (count($this->buffer) == 0 && !$this->complete) {
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
        // sets current to 0, complete to false
        $this->setTotal($this->total);
        // unbuild
        $this->buffer->clear();
        // removes current line
        $this->buffer->write("\r" . Ansi::CLEAR_LINE);
        //renders
        $this->buffer->flush($this->output);
        return $this;
    }

    /**
     * Set the progress to complete and end the line
     *
     * @return static
     */
    public function end(): self {
        // don't do anything if already complete
        if (!$this->complete) {
            // build and triggers complete
            $this->setCurrent($this->total);
            //Render
            $this->out();
        }
        return $this;
    }

    ////////////////////////////   Interfaces   ////////////////////////////

    /** {@inheritdoc} */
    public function render(Output $output) {
        $this->buffer->flush($output);
    }

    /**
     * Increments the progress as steps and render
     *  Useful to show progress on multiple operations
     *  Operations are completed during yield
     *  eg: ORM: get a list of ids, and during yield loading these ids foreach($progress->setTotal(count($ids)) as $index) {$id = $ids[$index]; ...}
     *
     * @return Generator<int,int> $next => $current
     */
    public function getIterator() {
        // reset progress
        $this->setTotal($this->total);
        $i = 0;
        while ($i < $this->total) {
            yield $i + 1 => $i;
            //after yield so $progress->setLabel('My changing label') is rendered
            $this->setCurrent($i + 1)->out();
            // if during yield done $progress->end() or $progress->setCurrent($total)
            if ($this->complete) break;
            $i++;
        }
    }

}

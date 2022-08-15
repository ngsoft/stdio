<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

use IteratorAggregate;
use NGSOFT\{
    DataStructure\ClassIterator, STDIO, STDIO\Events\ProgressComplete, STDIO\Events\ProgressStep, STDIO\Helpers\ProgressBar\Element, STDIO\Outputs\Output,
    STDIO\Outputs\Renderer, STDIO\Styles\Styles, Traits\DispatcherAware
};
use Stringable,
    Traversable;
use function NGSOFT\Tools\iterate_all;

class ProgressBar implements Stringable, IteratorAggregate, Renderer
{

    use DispatcherAware;

    protected bool $isCompleted = false;
    protected float $percent = 0.0;
    protected ?ClassIterator $iterator = null;
    protected ?Output $output = null;

    /** @var Element[] */
    protected array $elements = [];

    public function __construct(
            protected ?Styles $styles,
            protected int $total = 100,
            protected int $current = 0
    )
    {
        $this->styles ??= STDIO::getCurrentInstance()->getStyles();
    }

    public function increment(int $value = 1): void
    {
        $this->setCurrent($this->current + $value);
    }

    public function decrement(int $value = 1): void
    {
        $this->increment($value * -1);
    }

    protected function all(): ClassIterator
    {
        return $this->iterator ??= new ClassIterator(Element::class, $this->elements);
    }

    /**
     * Completes the progress bar
     */
    public function end(): void
    {
        $this->setCurrent($this->total);
        if ( ! $this->isCompleted) {
            $this->dispatchEvent(new ProgressComplete($this))->onEvent();
            $this->isCompleted = true;
        }
    }

    public function reset(): void
    {
        $this->current = 0;
        $this->percent = 0.0;
    }

    public function getPercent(): float
    {
        return $this->percent;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getCurrent(): int
    {
        return $this->current;
    }

    public function setTotal(int $total): void
    {

        $this->total = max(1, $total);
        $this->reset();

        iterate_all($this->all()->setTotal($total));
    }

    public function setCurrent(int $current): void
    {
        $this->current = min($current, $this->total);
        $this->percent = round($this->current / $this->total, 2);
        iterate_all($this->all()->setCurrent($current));

        $this->dispatchEvent(new ProgressStep($this))->onEvent();
    }

    public function getOutput(): Output
    {
        return $this->output ??= STDIO::getCurrentInstance()->getErrorOutput();
    }

    public function setOutput(Output $output): void
    {
        $this->output = $output;
    }

    public function render(Output $output): void
    {
        $output->write($this);
    }

    public function getIterator(): Traversable
    {
        yield from $this->elements;
    }

    public function __toString(): string
    {
        /** @var Element $element */
        $result = '';
        foreach ($this as $element) {

            if ($element->isVisible()) {
                $result .= (string) $element;
            }
        }

        return $result;
    }

}

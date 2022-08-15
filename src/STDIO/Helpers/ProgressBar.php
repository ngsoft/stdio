<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

use IteratorAggregate;
use NGSOFT\{
    STDIO, STDIO\Events\ProgressComplete, STDIO\Events\ProgressStarted, STDIO\Events\ProgressStep, STDIO\Helpers\ProgressBar\Element, STDIO\Outputs\Output,
    STDIO\Outputs\Renderer, STDIO\Styles\Styles, Traits\DispatcherAware
};
use Stringable,
    Traversable;

class ProgressBar implements Stringable, IteratorAggregate, Renderer
{

    use DispatcherAware;

    protected bool $isCompleted = false;
    protected bool $started = false;
    protected float $percent = 0.0;
    protected ?Output $output = null;

    /** @var Element[] */
    protected array $elements = [];

    public function __construct(
            protected ?Styles $styles = null,
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

    /**
     * Call method in all elements
     */
    protected function call(string $method, mixed ...$arguments): void
    {
        foreach ($this->getElements() as $element) {
            call_user_func_array([$element, $method], $arguments);
        }
    }

    public function start(): void
    {
        if ($this->started) {
            return;
        }
        $this->started = true;
        $this->dispatchEvent(new ProgressStarted($this))->onEvent();
    }

    /**
     * Completes the progress bar
     */
    public function finish(): void
    {

        if ($this->current < $this->total) {
            $this->setCurrent($this->total);
            return;
        }

        if ( ! $this->isCompleted) {
            $this->isCompleted = true;
            $this->dispatchEvent(new ProgressComplete($this))->onEvent();
        }
    }

    public function reset(): void
    {
        $this->current = 0;
        $this->percent = 0.0;
        $this->isCompleted = false;
    }

    public function addElement(Element $element): void
    {
        $this->elements[$element->getName()] = $element;
    }

    public function getElements(): array
    {
        return $this->elements;
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

        $this->call('update');
    }

    public function setCurrent(int $current): void
    {

        $this->current = min($current, $this->total);
        $this->percent = round($this->current / $this->total, 2);
        $this->call('update');
        $this->dispatchEvent(new ProgressStep($this))->onEvent();

        if ($current >= $this->total && ! $this->isCompleted) {
            $this->finish();
        }
    }

    public function getOutput(): Output
    {
        return $this->output ??= STDIO::getCurrentInstance()->getErrorOutput();
    }

    public function setOutput(Output $output): void
    {
        $this->output = $output;
    }

    /** {@inheritdoc} */
    public function render(Output $output): void
    {
        $output->write($this);
    }

    /**
     * Displays the progress bar to the output
     */
    public function display(): void
    {
        $this->render($this->getOutput());
    }

    public function getIterator(): Traversable
    {
        yield from $this->elements;
    }

    public function __toString(): string
    {
        /** @var Element $element */
        $result = '';
        foreach ($this->elements as $element) {

            if ($element->isVisible()) {
                $result .= (string) $element;
            }
        }

        return $result;
    }

}

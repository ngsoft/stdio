<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

use IteratorAggregate;
use NGSOFT\{
    DataStructure\ClassIterator, STDIO\Helpers\ProgressBar\Element, STDIO\Styles\Styles, Traits\DispatcherAware
};
use Stringable,
    Traversable;

class ProgressBar implements Stringable, IteratorAggregate
{

    use DispatcherAware;

    protected bool $isCompleted = false;
    protected float $percent = 0.0;
    protected ?ClassIterator $iterator = null;

    /** @var Element[] */
    protected array $elements = [];

    public function __construct(
            protected ?Styles $styles,
            protected int $total = 100,
            protected int $current = 0
    )
    {
        $this->styles ??= new Styles();
    }

    protected function all(): ClassIterator
    {
        return $this->iterator ??= new ClassIterator(Element::class, $this->elements);
    }

    public function setTotal(int $total): void
    {
        $this->total = max(1, $total);
        $this->reset();
    }

    public function setCurrent(int $current): void
    {
        $this->current = min($current, $this->total);
        $this->percent = round($this->current / $this->total, 2);
        $this->all()->setCurrent($current);
    }

    public function reset(): void
    {
        $this->setCurrent(0);
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

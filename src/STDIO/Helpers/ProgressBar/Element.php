<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers\ProgressBar;

use Countable;
use NGSOFT\STDIO\{
    Helpers\ProgressBar, Styles\Style, Styles\Styles
};
use Stringable;
use function class_basename;

abstract class Element implements Stringable, Countable
{

    protected string $name;
    protected bool $show = true;
    protected ?string $value = null;
    protected ?Style $style = null;
    protected float $percent = 0.0;

    /**
     * Update the display when progress is changed
     */
    abstract protected function update(): string;

    /**
     * Get the reserved length without style
     */
    abstract protected function getLength(): int;

    public function __construct(
            protected ProgressBar $parent,
            protected Styles $styles
    )
    {
        $this->name = strtolower(class_basename(static::class));
        $this->update();
    }

    protected function reset(): void
    {
        $this->current = 0;
        $this->percent = 0.0;
        $this->value = null;
    }

    protected function getSibling(bool $visible = true): \Traversable
    {

        /** @var self $element */
        foreach ($this->parent->getElements() as $element) {
            if ($element === $this) {
                continue;
            }

            if ( ! $element->isVisible() && $visible) {
                continue;
            }
            yield $element;
        }
    }

    public function setStyle(Style $style): void
    {
        $this->style = $style;
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
        $this->value = null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getStyle(): Style
    {
        return $this->style ??= new Style();
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

    protected function getValue()
    {
        return $this->value ??= $this->update();
    }

    public function isVisible(): bool
    {
        return $this->show;
    }

    public function show(): void
    {
        $this->show = true;
    }

    public function hide(): void
    {
        $this->show = false;
    }

    public function isComplete(): bool
    {
        return $this->current >= $this->total;
    }

    public function count(): int
    {
        return $this->getLength();
    }

    public function __toString(): string
    {
        return $this->getValue();
    }

}

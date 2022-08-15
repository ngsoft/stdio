<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers\ProgressBar;

use NGSOFT\STDIO\Styles\{
    Style, Styles
};
use Stringable;
use function class_basename;

abstract class Element implements Stringable
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

    public function __construct(
            protected Styles $styles,
            protected int $total,
            protected int $current = 0
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

    public function __toString(): string
    {
        return $this->getValue();
    }

}

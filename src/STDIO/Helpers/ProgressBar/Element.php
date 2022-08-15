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
    protected string $value = '';
    protected ?Style $style = null;

    /**
     * Update the display when progress is changed
     */
    abstract public function update(): void;

    /**
     * Get the reserved length without style
     */
    abstract public function getLength(): int;

    public function __construct(
            protected ProgressBar $parent,
            protected Styles $styles
    )
    {
        $this->name = strtolower(class_basename(static::class));

        $this->update();
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
        return $this->parent->getPercent();
    }

    public function getTotal(): int
    {
        return $this->parent->getTotal();
    }

    public function getCurrent(): int
    {
        return $this->parent->getCurrent();
    }

    protected function getValue()
    {
        return $this->value;
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
        return $this->getCurrent() >= $this->getTotal();
    }

    public function count(): int
    {
        return $this->getLength();
    }

    public function __toString(): string
    {
        $result = $this->getValue();

        if ($this->style) {
            $result = $this->style->format($result, $this->styles->colors);
        }

        return $result;
    }

}

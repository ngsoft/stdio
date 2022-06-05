<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Components;

use Countable,
    NGSOFT\STDIO\Styles\Style,
    Stringable;
use function mb_strlen;

class Element implements Countable, Stringable
{

    protected ?Style $style = null;
    protected string $value = '';
    protected int $length = 0;

    /** @var self[] */
    protected array $children = [];

    public function __construct(
            public readonly string $name
    )
    {

    }

    public function appendChild(self $element): static
    {

        array_push($this->children, $element);
        return $this;
    }

    public function prependChild(self $element): static
    {

        array_unshift($this->children, $element);
        return $this;
    }

    public function getStyle(): Style
    {
        return $this->style;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setStyle(Style $style)
    {
        $this->style = $style;
        return $this;
    }

    public function setValue(string $value)
    {
        $this->value = $value;
        $this->length = mb_strlen($value);
        return $this;
    }

    public function count(): int
    {

        $length = $this->length;
        foreach ($this->children as $element) {
            $length += count($element);
        }
        return $this->length;
    }

    public function render(): string
    {

        $result = '';
        foreach ($this->children as $element) {
            $result .= $element->render();
        }
        $result .= $this->value;
        return $this->style?->format($result) ?? $result;
    }

    public function __toString(): string
    {
        return $this->render();
    }

}

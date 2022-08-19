<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\STDIO\Styles\Style,
    Stringable;

/**
 * A Formatted Message
 */
class Message implements Stringable, \Countable
{

    protected string $text = '';
    protected ?string $formatted = null;
    protected ?Style $style = null;

    public function __clone()
    {
        $this->formatted = $this->style = null;
        $this->text = '';
    }

    public function format(string $text, ?Style $style = null): static
    {
        $clone = clone $this;
        $clone->text = $text;
        $clone->style = $style;
        return $clone;
    }

    public function count(): int
    {
        return mb_strlen($this->text);
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function __toString(): string
    {
        return $this->style?->format($this->text) ?? $this->text;
    }

}

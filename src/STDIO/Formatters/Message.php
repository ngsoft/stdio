<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use Countable,
    NGSOFT\STDIO\Styles\Style,
    Stringable;
use function mb_strlen;

/**
 * A Formatted Message
 */
class Message implements Stringable, Countable
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

    public function getText(): string
    {
        return $this->text;
    }

    public function getFormatted(): string
    {
        return $this->formatted ??= $this->style?->format($this->text) ?? $this->text;
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
        return $this->getFormatted();
    }

}

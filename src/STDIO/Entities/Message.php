<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Entities;

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

    public static function create(string $text = '', ?Style $style = null): static
    {
        $ins = new static();
        $ins->text = $text;
        $ins->style = $style;

        return $ins;
    }

    public function __clone(): void
    {
        $this->formatted = $this->style = null;
        $this->text = '';
    }

    public function format(string $text, ?Style $style = null): static
    {
        $this->style = $style;
        return $this->setText($text);
    }

    public function setStyle(Style $style): static
    {
        $this->style = $style;
        return $this;
    }

    public function setText(string $text)
    {
        $this->formatted = null;
        $this->text = $text;
        return $this;
    }

    public function setFormatted(string $formatted): static
    {
        $this->formatted = $formatted;
        return $this;
    }

    public function getStyle(): ?Style
    {
        return $this->style;
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

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

    public function __construct(protected string $text = '')
    {

    }

    public static function create(string $text = ''): static
    {
        return new static($text);
    }

    public function setText(string $text)
    {
        $this->text = $text;
        return $this;
    }

    public function getText(): string
    {
        return $this->text;
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
        return $this->text;
    }

}

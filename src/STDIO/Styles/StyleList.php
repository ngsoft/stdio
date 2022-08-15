<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use ArrayAccess,
    Countable,
    IteratorAggregate,
    NGSOFT\Facades\Terminal,
    Traversable;

class StyleList implements ArrayAccess, IteratorAggregate, Countable
{

    /** @var Style[] */
    protected static $_styles = [];
    protected static $_formats = [];
    protected bool $colors;

    /** @var Style[] */
    protected array $styles = [];

    public function __construct(
            bool $forceColorSupport = null
    )
    {
        $this->colors = $forceColorSupport ??= Terminal::supportsColors();
    }

    public function offsetExists(mixed $offset): bool
    {

    }

    public function offsetGet(mixed $offset): mixed
    {

    }

    public function offsetSet(mixed $offset, mixed $value): void
    {

    }

    public function offsetUnset(mixed $offset): void
    {

    }

    public function count(): int
    {

    }

    public function getIterator(): Traversable
    {

    }

}

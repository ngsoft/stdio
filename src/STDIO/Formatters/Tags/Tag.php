<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\STDIO\Styles\{
    Style, Styles
};
use function class_basename;

abstract class Tag implements \IteratorAggregate
{

    public const SELF_CLOSING = 0;
    public const HAS_CONTENTS = 1;

    public readonly string $name;

    /** @var array<string, string[]> */
    protected array $attributes = [];

    public function __construct(
            protected ?Styles $styles = null
    )
    {
        $this->styles ??= new Styles();
        $this->name = strtolower(class_basename(static::class));
    }

    public function getName(): string
    {
        return $this->name;
    }

    abstract public function getFormat(array $attributes): string;

    abstract public function getType(): int;

    public function hasAttribute(string $attribute): bool
    {
        return isset($this->attributes[$attribute]);
    }

    public function getAttribute(string $attribute): ?array
    {
        return $this->attributes[$attribute] ?? null;
    }

    public function getFirstAttribute(string $attribute): ?string
    {

        return $this->attributes[$attribute][0] ?? $this->hasAttribute($attribute) ? '' : null;
    }

    public function getStyle(array $attributes): Style
    {
        return $this->styles->createStyleFromAttributes($attributes);
    }

    public function getIterator(): \Traversable
    {
        yield from $this->attributes;
    }

}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use InvalidArgumentException;
use NGSOFT\STDIO\Styles\{
    Style, Styles
};
use function class_basename,
             is_stringable;

abstract class Tag implements \Stringable, \IteratorAggregate
{

    protected string $name;

    /** @var array<string, string[]> */
    protected array $attributes = [];
    protected ?Style $style = null;

    /**
     * Checks if tag manages contents
     */
    abstract public function isSelfClosing(): bool;

    /**
     * Get Formated string
     */
    abstract public function format(string $message): string;

    /**
     * Checks if tag manages specific attributes
     */
    public function managesAttributes(array $attributes): bool
    {
        return isset($attributes[$this->getName()]);
    }

    public function __construct(
            protected ?Styles $styles = null
    )
    {
        $this->styles ??= new Styles();
        $this->name = strtolower(class_basename(static::class));
    }

    /**
     * Get a new instance with the specified attributes
     */
    public function createFromAttributes(array $attributes, ?Styles $styles = null): static
    {
        $instance = new static($styles);
        $instance->attributes = $attributes;
        return $instance;
    }

    /**
     * Get tag name
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Transform value to a string
     */
    protected function getValue(mixed $value): string
    {
        if ( ! is_stringable($value) || is_null($value)) {
            throw new InvalidArgumentException(sprintf('Value of type %s is not Stringable.', get_debug_type($value)));
        }

        if (is_object($value)) {
            $value = (string) $value;
        } elseif ( ! is_string($value)) {
            $value = json_encode($value);
        }

        return $value;
    }

    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
    }

    public function setAttribute(string $attr, mixed $value): void
    {
        $this->attributes[$attr] = [$this->getValue($value)];
    }

    public function addAttribute(string $attr, mixed $value): void
    {

        $this->attributes[$attr] ??= [];
        $this->attributes[] = $this->getValue($value);
    }

    public function hasAttribute(string $attr): bool
    {
        return isset($this->attributes[$attr]);
    }

    public function getAttribute(string $attr): ?array
    {
        return $this->attributes[$attr] ?? null;
    }

    public function getFirstAttribute(string $attr): ?string
    {

        if ( ! $this->hasAttribute($attr)) {
            return null;
        }

        return $this->attributes[$attr][0] ?? '';
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getStyle(): Style
    {
        return $this->styles->createStyleFromAttributes($this->attributes, $this->getCode());
    }

    protected function getCode(): string
    {
        return implode(
                ';',
                array_map(
                        fn($attr) => implode(',', $attr),
                        $this->attributes
                )
        );
    }

    public function getIterator(): \Traversable
    {
        yield from $this->attributes;
    }

    public function __toString(): string
    {
        return sprintf('<%s>', $this->getCode());
    }

}

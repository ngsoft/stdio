<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use InvalidArgumentException,
    IteratorAggregate;
use NGSOFT\STDIO\Styles\{
    Style, Styles
};
use Stringable,
    Traversable;
use function class_basename,
             get_debug_type,
             is_stringable,
             preg_exec;

abstract class Tag implements Stringable, IteratorAggregate
{

    protected string $name;
    protected bool $selfClosing = false;

    /** @var array<string, string[]> */
    protected array $attributes = [];
    protected ?string $code = null;
    protected ?Style $style = null;

    ////////////////////////////   Static Methods   ////////////////////////////

    /**
     * Parse tag Attributes
     */
    final public static function getTagAttributesFromCode(string $code): array
    {

        static $cache = [];

        if ( ! isset($cache[$code])) {
            $cache[$code] = [];
            $attributes = &$cache[$code];
            foreach (preg_split('#;+#', $code) as $attribute) {
                [, $key, $val] = preg_exec('#([^=]+)(?:=(.+))?#', $attribute);
                $key = strtolower(trim($key));

                $attributes[$key] = isset($val) ? [$val] : [];
            }
        }

        return $cache[$code];
    }

    ////////////////////////////   Overrides   ////////////////////////////

    /**
     * Checks if tag manages contents
     */
    public function isSelfClosing(): bool
    {
        return $this->selfClosing;
    }

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

    /**
     * Tag priority over the others
     */
    public function getPriority(): int
    {
        return 16;
    }

    ////////////////////////////   Implementation   ////////////////////////////


    public function __construct(
            protected ?Styles $styles = null
    )
    {
        $this->styles ??= new Styles();
        $this->name = strtolower(class_basename(static::class));
    }

    /**
     * Get a new instance with the specified code
     */
    public function createFromCode(string $code): static
    {
        $instance = new static($this->styles);
        $instance->attributes = self::getTagAttributesFromCode($code);
        $instance->code = $code;
        return $instance;
    }

    /**
     * Get a new instance with the specified attributes
     */
    public function createFromAttributes(array $attributes): static
    {
        $instance = new static($this->styles);
        $instance->attributes = $attributes;
        return $instance;
    }

    /**
     * Checks if tag manages code
     */
    public function managesCode(string $code): bool
    {
        return $this->managesAttributes(self::getTagAttributesFromCode($code));
    }

    /**
     * Register Styles instance
     */
    public function setStyles(?Styles $styles): void
    {
        $this->styles = $styles;
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
        $this->attributes[$attr][] = $this->getValue($value);
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
        return $this->attributes[$attr][0] ?? null;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getStyle(): Style
    {
        return $this->style ??= $this->styles->createStyleFromAttributes($this->attributes, $this->getCode());
    }

    public function getCode(): string
    {

        if ( ! $this->code) {
            $code = &$this->code;
            $code = '';
            foreach ($this->attributes as $attr => $values) {
                if ( ! empty($code)) {
                    $code .= ';';
                }
                $code .= $attr;
                if (count($values)) {
                    $code .= sprintf('=%s', implode(',', $values));
                }
            }
        }

        return $this->code;
    }

    public function getIterator(): Traversable
    {
        yield from $this->attributes;
    }

    public function __clone(): void
    {

        $this->code = null;
        $this->attributes = [];
        $this->style = null;
    }

    public function __toString(): string
    {
        return sprintf('<%s>', $this->getCode());
    }

    public function __debugInfo(): array
    {
        return [
            'name' => $this->getName(),
            'tag' => $this->__toString(),
            'attributes' => $this->attributes
        ];
    }

}

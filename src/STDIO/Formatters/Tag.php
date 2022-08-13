<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use InvalidArgumentException,
    IteratorAggregate;
use NGSOFT\{
    DataStructure\PrioritySet, STDIO\Styles\Style, STDIO\Styles\Styles
};
use Stringable,
    Traversable;
use function class_basename,
             get_debug_type,
             is_stringable,
             NGSOFT\Filesystem\require_all_once,
             preg_exec;

abstract class Tag implements Stringable, IteratorAggregate
{

    protected string $name;

    /** @var array<string, string[]> */
    protected array $attributes = [];
    protected ?string $code = null;

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
                $attributes[strtolower(trim($key))] = isset($val) ? array_map(fn($v) => trim($v), preg_split('#,+#', $val)) : [];
            }
        }

        return $cache[$code];
    }

    ////////////////////////////   Overrides   ////////////////////////////

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

        if (is_null($this->code)) {
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

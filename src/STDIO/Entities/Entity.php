<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Entities;

use Countable,
    InvalidArgumentException,
    JsonException;
use NGSOFT\STDIO\{
    Formatters\Formatter, Outputs\OutputInterface, Outputs\Renderer, Styles\Style, Styles\StyleList
};
use Stringable,
    Traversable;
use function get_debug_type,
             is_stringable;

abstract class Entity implements Stringable, Countable, Renderer, Formatter
{

    /** @var array<string, string> */
    protected array $attributes = [];
    protected bool $standalone = false;
    protected bool $active = false;
    protected ?self $parent = null;
    protected ?Style $style = null;
    protected ?string $formatted = null;

    /** @var self[]|Message[] */
    protected array $children = [];

    public static function create(string $tag = '', ?StyleList $styles = null)
    {
        return new static($tag, $styles);
    }

    public function __construct(
            protected string $tag = '',
            protected ?StyleList $styles = null
    )
    {
        $this->styles ??= new StyleList();
        $this->attributes = $this->styles->getParamsFromStyleString($tag);
    }

    /**
     * The priority in the stack [1-INF]
     * a highest number gets executed first
     */
    abstract public static function getPriority(): int;

    /**
     * Checks attributes to define if that entity gets executed
     * (only the first match is executed, it's why priority must be set)
     */
    abstract public static function matches(array $attributes): bool;

    /**
     * Write the message into the entity
     */
    public function write(string $message): void
    {

        $this->formatted = null;

        if (empty($message)) {
            return;
        }
        $this->children[] = Message::create($message);
    }

    /**
     * Empty the message stack
     */
    public function clear(): void
    {

        $this->formatted = null;

        foreach ($this->children as $index => $entity) {
            if ($entity instanceof self) {
                $entity->clear();
                $this->removeChild($entity);
                continue;
            }
            unset($this->children[$index]);
        }
    }

    /**
     * Flag to check if entity has contents
     * if set to true, the entity does not have contents
     */
    public function isStandalone(): bool
    {
        return $this->standalone;
    }

    /**
     * Flag to check if the current entity is the active entity
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active = true): static
    {

        if ($active) {
            /** @var self $entity */
            foreach ($this->getChildrenEntities() as $entity) {
                $entity->setActive(false);
            }

            // set tree to root => $this to true
            $this->parent?->setActive(true);
        }

        $this->active = $active;

        return $this;
    }

    /**
     * Adds an entity as a children
     */
    public function appendChild(self $entity): void
    {

        if ($entity === $this || $entity === $this->parent || in_array($entity, $this->children)) {
            throw new InvalidArgumentException('Cannot append Entity.');
        }
        $this->formatted = null;

        $this->children[] = $entity;
        $entity->parent = $this;
    }

    public function getChildrenEntities(): Traversable
    {
        $children = $this->children;

        foreach ($children as $index => $entity) {

            if ($entity instanceof self) {
                yield $index => $entity;
            }
        }
    }

    /**
     * Removes a child
     */
    public function removeChild(self $entity): void
    {

        if ($entity->isActive()) {
            return;
        }

        $index = array_search($entity, $this->children);
        if (false !== $index) {
            $this->formatted = null;
            $entity->parent = null;
            unset($this->children[$index]);
        }
    }

    public function getStyles(): StyleList
    {
        return $this->styles;
    }

    public function getStyle(): Style
    {
        return $this->style ??= $this->styles->createStyleFromParams($this->attributes);
    }

    public function setStyle(Style $style): void
    {
        $this->style = $style;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function getTag(): string
    {
        return $this->tag;
    }

    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Transform value to a string
     */
    public function getValue(mixed $value): ?string
    {

        if (is_null($value)) {
            return $value;
        }

        if ( ! is_stringable($value)) {
            throw new InvalidArgumentException(sprintf('Value of type %s is not int|float|bool|string|Stringable.', get_debug_type($value)));
        }

        if (is_object($value)) {
            $value = (string) $value;
        } elseif ( ! is_string($value)) {
            $value = json_encode($value);
        }

        return $value;
    }

    /**
     * Helper to get attribute value as int
     */
    public function getInt(mixed $value, int $default): int
    {
        return is_int($value) ? $value : $default;
    }

    /**
     * Helper to get attribute value as float
     */
    public function getFloat(mixed $value, float $default): float
    {

        if (is_int($value)) {
            $value = floatval($value);
        }
        return is_float($value) ? $value : $default;
    }

    /**
     * Helper to get attribute value as bool
     */
    public function getBool(mixed $value, bool $default): bool
    {
        return is_bool($value) ? $value : $default;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $attr): mixed
    {
        $value = $this->attributes[$attr] ?? null;

        if (is_string($value)) {

            try {
                $value = json_decode($value, flags: JSON_THROW_ON_ERROR);
            } catch (JsonException) {

            }
        }

        return $value;
    }

    public function hasAttribute(string $attr): bool
    {
        return ! is_null($this->getAttribute($attr));
    }

    /**
     * @phan-suppress PhanTypeMismatchProperty
     */
    public function setAttribute(string $attr, mixed $value): void
    {
        if (is_null($value)) {
            $this->removeAttribute($attr);
            return;
        }
        $this->attributes[$attr] = $this->getValue($value);
    }

    public function removeAttribute(string $attr): void
    {
        unset($this->attributes[$attr]);
    }

    public function format(string|Stringable $message): string
    {
        $message = (string) $message;

        if (empty($message)) {
            return $message;
        }

        return $this->getStyle()->format($message);
    }

    public function render(OutputInterface $output): void
    {
        $output->write($this);
    }

    protected function build(): string
    {
        if ( ! $this->formatted) {
            $str = '';
            foreach ($this->children as $entity) {
                $str .= (string) $entity;
            }
            $this->formatted = $this->format($str);
        }

        return $this->formatted;
    }

    public function count(): int
    {
        $len = 0;
        foreach ($this->children as $entity) {
            $len += count($entity);
        }
        return $len;
    }

    public function __toString(): string
    {
        return $this->build();
    }

    public function __debugInfo(): array
    {
        return [
            'parent' => $this->parent,
            'children' => $this->children,
            'standalone' => $this->standalone,
            'active' => $this->active,
            'style' => $this->getStyle(),
        ];
    }

}

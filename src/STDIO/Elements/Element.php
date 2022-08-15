<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

use InvalidArgumentException;
use NGSOFT\{
    STDIO, STDIO\Styles\Style, STDIO\Styles\StyleList
};
use RuntimeException;
use function get_debug_type,
             is_stringable;

class Element
{

    protected ?self $parent = null;

    /** @var self[] */
    protected array $children = [];

    /** @var array<string, string> */
    protected array $attributes = [];
    protected string $text = '';
    protected bool $isStandalone = false;
    protected ?Style $style = null;

    public function __construct(
            protected string $tag,
            protected ?StyleList $styles = null
    )
    {
        $this->styles ??= STDIO::getCurrentInstance()->getStyles();
    }

    /**
     * Transform value to a string
     */
    protected function getValue(mixed $value): ?string
    {

        if (is_null($value)) {
            return $value;
        }

        if ( ! is_stringable($value)) {
            throw new InvalidArgumentException(sprintf('Value of type %s is not Stringable.', get_debug_type($value)));
        }

        if (is_object($value)) {
            $value = (string) $value;
        } elseif ( ! is_string($value)) {
            $value = json_encode($value);
        }

        return $value;
    }

    public function isStandalone(): bool
    {
        return $this->isStandalone;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }

    public function getAttribute(string $attr): mixed
    {
        $value = $this->getValue($this->attributes[$attr] ?? null);

        if (is_string($value)) {

            try {
                $value = json_decode($value, flags: JSON_THROW_ON_ERROR);
            } catch (\JsonException) {

            }
        }

        return $value;
    }

    public function hasAttribute(string $attr): bool
    {
        return ! is_null($this->getAttribute($attr));
    }

    public function setAttribute(string $attr, mixed $value): void
    {
        $this->attributes[$attr] = $this->getValue($value);
    }

    public function removeAttribute(string $attr): void
    {
        unset($this->attributes[$attr]);
    }

    public function setContents(string $text)
    {
        if ( ! $this->isStandalone) {
            $this->text = $text;
        }
    }

    public function appendChild(self $element): void
    {

        if ($element === $this || $element === $this->parent) {
            throw new RuntimeException('Cannot append Element.');
        }

        if ( ! empty($this->text)) {
            $clone = clone $this;
            $this->text = '';
            $clone->parent = $this;
            $this->children[] = $clone;
        }

        $this->children[] = $element;
        $element->parent = $this;
    }

    public function __clone(): void
    {
        $this->children = [];
        $this->parent = null;
    }

    public function __toString()
    {
        $text = '';

        foreach ($this->children as $element) {
            $text .= (string) $element;
        }

        if ( ! empty($this->text)) {
            $text .= $this->styles->createStyleFromParams($this->attributes)->format($this->text);
        }
        return $text;
    }

}

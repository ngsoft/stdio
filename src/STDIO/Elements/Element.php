<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

use Countable,
    InvalidArgumentException,
    JsonException;
use NGSOFT\{
    STDIO, STDIO\Styles\Style, STDIO\Styles\StyleList
};
use RuntimeException,
    Stringable;
use function get_debug_type,
             is_stringable,
             mb_strlen;

/**
 * @phan-file-suppress PhanUnusedPublicNoOverrideMethodParameter
 */
class Element implements Stringable, Countable
{

    protected ?self $parent = null;

    /** @var self[] */
    protected array $children = [];

    /** @var array<string, ?string> */
    protected array $attributes = [];
    protected Message $message;
    protected bool $isStandalone = false;
    protected bool $isClone = false;
    protected ?Style $style = null;
    protected bool $pulled = false;

    public static function create(
            string $tag = '',
            ?StyleList $styles = null
    ): static
    {
        return new static($tag, $styles);
    }

    public function __construct(
            protected string $tag = '',
            protected ?StyleList $styles = null
    )
    {
        $this->styles ??= STDIO::getCurrentInstance()->getStyles();
        $this->message = new Message();
        $this->attributes = $this->styles->getParamsFromStyleString($tag);
    }

    public function onPop(): void
    {

    }

    public function onPush(): void
    {

    }

    public function onPull(): void
    {

    }

    public static function getPriority(): int
    {
        return 2;
    }

    public static function managesAttributes(array $attributes): bool
    {
        return true;
    }

    public function write(string $contents): void
    {
        $this->pulled = false;
        $this->message->format($this->getStyle()->format($contents), $contents);
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

    public function getTag(): string
    {
        return $this->tag;
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
            } catch (JsonException) {

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

    public function getFormated(): string
    {
        $result = '';

        foreach ($this->children as $elem) {
            $result .= $elem->getFormated();
        }

        $result .= $this->message->getFormated();

        return $result;
    }

    public function getRaw(): string
    {
        $result = '';

        foreach ($this->children as $elem) {
            $result .= $elem->getRaw();
        }

        $result .= $this->message->getText();

        return $result;
    }

    public function getStyles(): StyleList
    {
        return $this->styles;
    }

    public function getStyle(): Style
    {
        return $this->style ??= $this->styles->createStyleFromParams($this->attributes, $this->tag);
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function appendChild(self $element): void
    {

        if ($element === $this || $element === $this->parent) {
            throw new RuntimeException('Cannot append Element.');
        }

        if ( ! $this->message->isEmpty()) {
            $clone = clone $this;
            $clone->parent = $this;
            $this->children[] = $clone;
        }

        $this->reset();

        $this->children[] = $element;
        $element->parent = $this;
    }

    public function removeChild(self $elem): void
    {
        $index = array_search($elem, $this->children);

        if (false !== $index) {
            $this->children = array_splice($this->children, $index, 1);
        }

        $elem->parent = null;
    }

    public function __clone(): void
    {
        $this->message = clone $this->message;
        $this->children = [];
        $this->parent = null;
        $this->isClone = true;
    }

    public function reset(): void
    {
        $this->message->clear();
    }

    public function pull(): string
    {
        if ($this->pulled && $this->parent) {
            return '';
        }
        $this->pulled = true;

        $this->onPull();

        $text = $this->getFormated();

        $this->reset();

        if ($this->isClone) {
            $this->parent?->removeChild($this);
        }
        return $text;
    }

    public function count(): int
    {
        return mb_strlen($this->getRaw());
    }

    public function __toString(): string
    {
        return $this->message->getFormated();
    }

    public function __debugInfo(): array
    {

        return [
            'tag' => $this->tag,
            'isStandalone' => $this->isStandalone,
            'isClone' => $this->isClone,
            'pulled' => $this->pulled,
            'attributes' => $this->attributes,
            'parent' => $this->parent,
            'children' => $this->children,
            'message' => $this->message,
        ];
    }

}

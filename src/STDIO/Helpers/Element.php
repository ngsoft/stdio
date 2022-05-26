<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

use Countable;
use NGSOFT\{
    Attributes\Property, STDIO\Styles\Style, Traits\PropertyAttributeAccess
};
use Stringable,
    ValueError;
use function get_debug_type,
             mb_strlen;

/**
 * @property-read int $length
 * @property string $value
 * @property Style $style
 */
class Element implements Stringable, Countable {

    use PropertyAttributeAccess;

    #[Property(readable: true, serializable: true)]
    protected int $length = 0;

    #[Property(readable: true, writable: true, serializable: true)]
    protected string $value = '';

    #[Property(readable: true, writable: true, serializable: true)]
    protected ?Style $style = null;

    /** @var self[] */
    protected array $children = [];

    public function addChild(Element $child): static {
        $this->children[] = $child;
        return $this;
    }

    public function removeChild(Element $child): static {
        $index = array_search($child, $this->children, true);
        if (false !== $index) unset($this->children[$index]);
        return $this;
    }

    public function setChildren(array $children): static {

        $this->children = [];
        foreach ($children as $child) {

            if ($child instanceof self === false) {
                throw new ValueError(sprintf('Invalid child type %s expected but %s given.', self::class, get_debug_type($child)));
            }
            $this->addChild($child);
        }

        return $this;
    }

    /**
     * @return Element[]
     */
    public function getChildren(): array {
        return $this->children;
    }

    public function getValue(): string {
        return $this->value;
    }

    /**
     * @phan-suppress PhanAccessReadOnlyMagicProperty
     * @param string $value
     * @return static
     */
    public function setValue(string $value): static {
        $this->value = $value;
        $this->length = mb_strlen($value);
        return $this;
    }

    public function getStyle(): ?Style {
        return $this->style;
    }

    public function setStyle(Style $style): static {
        $this->style = $style;
        return $this;
    }

    public function render(): string {
        if (count($this->children) > 0) {
            $result = '';
            foreach ($this->children as $element) {
                $result .= $element->render();
            }
            return $result;
        }
        if ($this->style) return $this->style->format($this->value);
        return $this->value;
    }

    public function __toString(): string {
        return $this->render();
    }

    public function count(): int {
        return $this->length;
    }

}

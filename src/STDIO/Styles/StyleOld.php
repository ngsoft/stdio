<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use Countable;
use NGSOFT\{
    Facades\Terminal, STDIO\Enums\Ansi, STDIO\Enums\BackgroundColor, STDIO\Enums\Color, STDIO\Enums\Format
};
use Stringable;
use function class_basename;

class StyleOld implements Stringable, Countable
{

    protected static ?bool $terminalSupportsColor = null;

    /** @var Format[]|Color[]|BackgroundColor[]|HexColor[]|BrightColor[] */
    protected array $styles = [];
    protected ?string $prefix = null;
    protected ?string $suffix = null;

    public function __construct(
            protected string $label = '',
            protected ?bool $colors = null
    )
    {
        $this->colors ??= self::$terminalSupportsColor ??= Terminal::supportsColors();
    }

    public function setLabel(string $label): static
    {
        $this->label = $label;
        return $this;
    }

    /**
     * Set formats for the style
     */
    public function setStyles(Format|Color|BackgroundColor|HexColor|BrightColor ...$styles): static
    {
        $this->styles = $styles;
        $this->prefix = $this->suffix = null;
        return $this;
    }

    public function getPrefix(): string
    {
        if ( ! $this->prefix && $this->colors) {
            $this->prefix = Ansi::ESCAPE . implode(';', array_map(fn($enum) => $enum->getValue(), $this->styles)) . Ansi::STYLE_SUFFIX;
        }

        return $this->prefix ??= '';
    }

    public function getSuffix(): string
    {
        if ( ! $this->suffix && $this->colors) {
            $this->suffix = Ansi::ESCAPE . implode(';', array_map(fn($enum) => $enum->getUnsetValue(), $this->styles)) . Ansi::STYLE_SUFFIX;
        }
        return $this->suffix ??= '';
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getStyles(): array
    {
        return $this->styles;
    }

    /**
     * Format message to include style
     */
    public function format(string|Stringable $message): string
    {
        return $this->getPrefix() . (string) $message . $this->getSuffix();
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function count(): int
    {
        return count($this->styles);
    }

    public function __debugInfo(): array
    {

        return [
            'label' => $this->getLabel(),
            'styles' => array_map(fn($enum) => class_basename(get_class($enum)) . '::' . $enum->getName(), $this->styles),
            'format' => $this->getPrefix() . $this->getLabel() . $this->getSuffix()
        ];
    }

    public function __toString(): string
    {
        return $this->getLabel();
    }

}

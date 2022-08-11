<?php

declare(strict_types=1);

namespace NGSOFT\STDIO;

use NGSOFT\{
    Facades\Terminal, STDIO\Enums\Ansi, STDIO\Enums\BackgroundColor, STDIO\Enums\Color, STDIO\Enums\Format
};
use Stringable;

class Style
{

    /** @var Format|Color|BackgroundColor[] */
    protected array $styles = [];
    protected ?string $prefix = null;
    protected ?string $suffix = null;

    public function __construct(
            protected string $label
    )
    {

    }

    public function setStyles(Format|Color|BackgroundColor ...$styles): void
    {
        $this->styles = $styles;
        $this->prefix = $this->suffix = null;
    }

    public function getPrefix(): string
    {
        if ( ! $this->prefix && count($this->styles)) {
            $this->prefix = Ansi::ESCAPE . implode(';', array_map(fn($enum) => $enum->getValue(), $this->styles)) . Ansi::STYLE_SUFFIX;
        }

        return $this->prefix ??= '';
    }

    public function getSuffix(): string
    {
        if ( ! $this->suffix && count($this->styles)) {
            $this->suffix = Ansi::ESCAPE . implode(';', array_map(fn($enum) => $enum->getUnsetValue(), $this->styles)) . Ansi::STYLE_SUFFIX;
        }

        return $this->suffix ??= '';
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Format message to include style
     */
    public function format(string|Stringable $message, bool $colors = null): string
    {
        $colors ??= Terminal::supportsColors();
        $message = (string) $message;
        return $colors ? sprintf("%s%s%s", $this->getPrefix(), $message, $this->getSuffix()) : $message;
    }

    public function __debugInfo(): array
    {

        return [
            'label' => $this->label,
            'styles' => array_map(fn($enum) => $enum->name, $this->styles),
            'format' => $this->format($this->label, true)
        ];
    }

}

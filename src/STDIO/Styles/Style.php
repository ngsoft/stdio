<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use NGSOFT\{
    Facades\Terminal, STDIO\Enums\Ansi, STDIO\Enums\BackgroundColor, STDIO\Enums\Color, STDIO\Enums\Format
};
use Stringable;

class Style
{

    protected string $label = '';
    protected string $prefix = '';
    protected string $suffix = '';
    protected bool $colors;

    /** @var string[]|int[] */
    protected array $set = [];

    /** @var int[] */
    protected array $unset = [];

    public function __construct(
            string $label = '',
            ?bool $colors = null
    )
    {
        $this->label = $label;
        $this->colors = $colors ?? Terminal::supportsColors();
    }

    public static function createEmpty(): static
    {
        return new static();
    }

    public static function createFrom(string $label, Format|Color|BackgroundColor|HexColor|BrightColor ...$styles)
    {

        return self::createEmpty()->withLabel($label)->withStyles(...$styles);
    }

    public function withStyles(Format|Color|BackgroundColor|HexColor|BrightColor ...$styles): static
    {
        $clone = clone $this;

        $set = $unset = [];
        $clone->prefix = $clone->suffix = '';

        foreach ($styles as $style) {
            $set[] = $style->getValue();
            $unset[] = $style->getUnsetValue();
        }

        $clone->set = array_unique($set);
        $clone->unset = array_unique($unset);

        return $clone;
    }

    public function withLabel(string $label): static
    {
        $clone = clone $this;
        $clone->label = $label;
        return $clone;
    }

    public function withColorSupport(bool $colors = true): static
    {
        $clone = clone $this;
        $clone->colors = $colors;
        return $clone;
    }

    public function withoutColorSupport(): static
    {
        return $this->withColorSupport(false);
    }

    public function isEmpty(): bool
    {
        return ! count($this->set);
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function getPrefix(): string
    {
        return $this->prefix ??= Ansi::ESCAPE . implode(';', $this->set) . Ansi::STYLE_SUFFIX;
    }

    public function getSuffix(): string
    {
        return $this->suffix ??= Ansi::ESCAPE . implode(';', $this->unset) . Ansi::STYLE_SUFFIX;
    }

    public function format(string|Stringable|array $messages): string
    {

        if ( ! is_array($messages)) {
            $messages = [$messages];
        }

        if (empty($messages)) {
            return '';
        }

        $result = '';
        foreach ($messages as $message) {
            $result .= $message;
        }

        if ($this->colors) {
            return $this->getPrefix() . $result . $this->getSuffix();
        }

        return $result;
    }

}

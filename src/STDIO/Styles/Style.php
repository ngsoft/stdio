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

    public function __construct()
    {
        $this->colors = Terminal::supportsColors();
    }

    public static function createEmpty(): static
    {
        return new static();
    }

    public static function createFrom(string $label, Format|Color|BackgroundColor|HexColor|BrightColor ...$styles): static
    {
        return self::createEmpty()->withLabel($label)->withFormats(...$styles);
    }

    public function withFormats(Format|Color|BackgroundColor|HexColor|BrightColor ...$formats): static
    {
        $clone = clone $this;
        $clone->set = $clone->unset = [];
        return $this->withAddedFormats(...$formats);
    }

    public function withAddedFormats(Format|Color|BackgroundColor|HexColor|BrightColor ...$formats)
    {
        $clone = clone $this;

        $set = $clone->set;
        $unset = $clone->unset;

        foreach ($formats as $format) {
            $set[] = $format->getValue();
            $unset[] = $format->getUnsetValue();
        }


        $clone->set = array_unique($set);
        $clone->unset = array_unique($unset);

        return $clone;
    }

    public function withAddedStyle(Style $style): static
    {
        $clone = clone $this;
        $clone->set = array_unique(array_merge($clone->set, $style->set));
        $clone->unset = array_unique(array_merge($clone->unset, $style->unset));
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
        if (empty($this->prefix) && count($this->set)) {
            foreach ($this->set as $set) {
                $this->prefix .= sprintf(Ansi::STYLE, (string) $set);
            }
        }
        return $this->prefix;
    }

    public function getSuffix(): string
    {

        if (empty($this->suffix) && count($this->unset)) {
            foreach ($this->unset as $unset) {
                $this->suffix .= sprintf(Ansi::STYLE, (string) $unset);
            }
        }

        return $this->suffix;
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

    public function __clone()
    {
        $this->prefix = $this->suffix = '';
    }

    public function __serialize(): array
    {
        return [$this->label, $this->set, $this->unset];
    }

    public function __unserialize(array $data): void
    {
        $this->colors = Terminal::supportsColors();
        @list($this->label, $this->set, $this->unset) = $data;
    }

}

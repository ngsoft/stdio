<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use NGSOFT\STDIO\Enums\{
    Ansi, BackgroundColor, Color, Format
};
use Stringable;

class Style
{

    protected string $label = '';
    protected string $prefix = '';
    protected string $suffix = '';
    protected bool $colors = '';

    /** @var string[]|int[] */
    protected array $set = [];

    /** @var int[] */
    protected array $unset = [];

    public static function createEmpty(): static
    {
        return new static();
    }

    public static function createFromFormats(string $label, Format|Color|BackgroundColor|HexColor|BrightColor ...$styles)
    {

        $instance = new static();

        $instance->label = $label;

        if (empty($styles)) {
            return $instance;
        }

        $set = $unset = [];

        foreach ($styles as $style) {

            $set[] = $style->getValue();
            $unset[] = $style->getUnsetValue();
        }
        $instance->set = array_unique($set);
        $instance->unset = array_unique($unset);
    }

    public function withLabel(string $label)
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

    public function format(string|Stringable|array $message): string
    {

        if ( ! is_array($message)) {
            $message = [$message];
        }

        $prefix = $suffix = '';
        if ($this->colors) {
            $prefix = $this->getPrefix();
            $suffix = $this->getSuffix();
        }
    }

}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use InvalidArgumentException;
use NGSOFT\STDIO\{
    Terminal, Values\BackgroundColor, Values\BrightBackgroundColor, Values\BrightColor, Values\Color, Values\Format
};

class Style {

    private ?string $label;
    private ?Color $color;
    private ?BackgroundColor $background;

    /** @var Format[] */
    private array $formats = [];
    private ?string $prefix;
    private ?string $suffix;
    private bool $supported;

    public function __construct(?bool $supported) {
        if (is_bool($supported)) $this->supported = $supported;
        else $this->supported = Terminal::create()->colors;
    }

    public function getPrefix(): string {
        if (!is_string($this->prefix)) {
            if (!$this->supported) return $this->prefix = '';
        }

        return $this->prefix;
    }

    public function getSuffix(): string {
        if (!is_string($this->suffix)) {
            if (!$this->supported) return $this->suffix = '';
        }
        return $this->suffix;
    }

    public function getLabel(): string {
        return $this->label ?? '';
    }

    ////////////////////////////   Creator   ////////////////////////////


    public function __clone() {
        if ($this->supported) $this->prefix = $this->suffix = null;
    }

    public function withLabel(string $label): static {
        $clone = clone $this;
        $clone->label = $label;
        return $clone;
    }

    public function withColor(Color|int $color): static {
        $clone = clone $this;
        if (is_int($color)) {
            if ($instance = Color::tryFrom($color) ?? BrightColor::tryFrom($color)) $clone->color = $instance;
            else throw new InvalidArgumentException(sprintf('Invalid color %d', $color));
        } else $clone->color = $color;
        return $clone;
    }

    public function withBackground(BackgroundColor|int $color): static {
        $clone = clone $this;
        if (is_int($color)) {


            if ($instance = BackgroundColor::tryFrom($color) ?? BrightBackgroundColor::tryFrom($color)) $clone->color = $instance;
            else throw new InvalidArgumentException(sprintf('Invalid background color %d', $color));
        } else $clone->color = $color;
        return $clone;
    }

    public function withFormats(Format|int ...$formats): static {

        $clone = clone $this;

        $result = [];

        foreach ($formats as $format) {

            if (is_int($format)) {
                if ($instance = Format::tryFrom($format)) {
                    $result[] = $instance;
                } else throw new InvalidArgumentException(sprintf('Invalid format %d', $format));
            } else $result[] = $instance;
        }

        $clone->formats = $result;
        return $clone;
    }

}

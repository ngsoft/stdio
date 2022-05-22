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
            /** @var Color $instance */
            foreach (Color::getValues() as $instance) {
                if ($instance->getValue() === $color) {
                    $clone->color = $instance;
                    return $clone;
                }
            }

            foreach (BrightColor::getValues() as $instance) {
                if ($instance->getValue() === $color) {
                    $clone->color = $instance;
                    return $clone;
                }
            }

            throw new InvalidArgumentException(sprintf('Invalid color %d', $color));
        }
        $clone->color = $color;
        return $clone;
    }

    public function withBackground(BackgroundColor|int $color): static {
        $clone = clone $this;
        if (is_int($color)) {
            /** @var BackgroundColor $instance */
            foreach (BackgroundColor::getValues() as $instance) {
                if ($instance->getValue() === $color) {
                    $clone->background = $instance;
                    return $clone;
                }
            }

            foreach (BrightBackgroundColor::getValues() as $instance) {
                if ($instance->getValue() === $color) {
                    $clone->background = $instance;
                    return $clone;
                }
            }

            throw new InvalidArgumentException(sprintf('Invalid background color %d', $color));
        }
        $clone->color = $color;
        return $clone;
    }

}

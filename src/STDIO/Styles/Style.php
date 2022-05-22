<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use InvalidArgumentException;
use NGSOFT\STDIO\{
    Terminal, Values\Ansi, Values\BackgroundColor, Values\BrightBackgroundColor, Values\BrightColor, Values\Color, Values\Format
};
use Stringable;

class Style {

    private string $label = '';
    private ?Color $color = null;
    private ?BackgroundColor $background = null;

    /** @var Format[] */
    private array $formats = [];
    private ?string $prefix = null;
    private ?string $suffix = null;

    /**
     * Mutes style if set to false
     * @var bool
     */
    private bool $supported;

    public function __construct(?bool $supported) {
        if (is_bool($supported)) $this->supported = $supported;
        else $this->supported = Terminal::create()->colors;
    }

    /**
     * Get style prefix
     *
     * @return string
     */
    public function getPrefix(): string {
        if (!is_string($this->prefix)) {
            if (!$this->supported) return $this->prefix = '';
            $result = '';
            $params = [];
            if (count($this->formats) > 0) $params = $this->formats;
            if ($this->color) $params[] = $this->color;
            if ($this->background) $params[] = $this->background;

            if (count($params) > 0) {
                $paramsInt = array_map(fn($val) => $val->getValue(), $params);
                $result = Ansi::ESCAPE . implode(';', $paramsInt) . Ansi::STYLE_SUFFIX;
            }
            $this->prefix = $result;
        }

        return $this->prefix;
    }

    /**
     * Get Style suffix
     *
     * @return string
     */
    public function getSuffix(): string {
        if (!is_string($this->suffix)) {
            if (!$this->supported) return $this->suffix = '';
            $result = '';
            $params = [];
            if (count($this->formats) > 0) $params = $this->formats;
            if ($this->color) $params[] = $this->color;
            if ($this->background) $params[] = $this->background;

            if (count($params) > 0) {
                $paramsInt = array_map(fn($val) => $val->getUnsetValue(), $params);
                $result = Ansi::ESCAPE . implode(';', $paramsInt) . Ansi::STYLE_SUFFIX;
            }
            $this->suffix = $result;
        }
        return $this->suffix;
    }

    public function getLabel(): string {
        return $this->label ?? '';
    }

    /**
     * Format message to include style
     *
     * @param string|Stringable $message
     * @return string
     */
    public function format(string|Stringable $message): string {
        if ($message instanceof Stringable) $message = $message->__toString();
        return sprintf("%s%s%s", $this->getPrefix(), $message, $this->getSuffix());
    }

    public function __debugInfo(): array {


        $formats = [];
        if ($this->color) $formats[] = sprintf('%s::%s', get_class($this->color), $this->color->label);
        if ($this->background) $formats[] = sprintf('%s::%s', get_class($this->background), $this->background->label);
        if (!empty($this->formats)) $formats = array_merge($formats, array_map(fn($val) => get_class($val) . '::' . $val->label, $this->formats));

        return [
            'label' => $this->label,
            'styles' => $formats,
            'format' => $this->format($this->label)
        ];
    }

    ////////////////////////////   Creator   ////////////////////////////

    /** {@inheritdoc} */
    public function __clone() {
        if ($this->supported) $this->prefix = $this->suffix = null;
    }

    /**
     * Set new label
     * @param string $label
     * @return static
     */
    public function withLabel(string $label): static {
        $clone = clone $this;
        $clone->label = $label;
        return $clone;
    }

    /**
     * Set color
     *
     * @param Color|int $color
     * @return static
     * @throws InvalidArgumentException
     */
    public function withColor(Color|int $color): static {
        $clone = clone $this;
        if (is_int($color)) {
            if ($instance = Color::tryFrom($color) ?? BrightColor::tryFrom($color)) $clone->color = $instance;
            else throw new InvalidArgumentException(sprintf('Invalid color %d', $color));
        } else $clone->color = $color;
        return $clone;
    }

    /**
     * Remove color
     *
     * @return static
     */
    public function withoutColor(): static {
        $clone = clone $this;
        $clone->color = null;
        return $clone;
    }

    /**
     * Set background color
     *
     * @param BackgroundColor|int $color
     * @return static
     * @throws InvalidArgumentException
     */
    public function withBackground(BackgroundColor|int $color): static {
        $clone = clone $this;
        if (is_int($color)) {


            if ($instance = BackgroundColor::tryFrom($color) ?? BrightBackgroundColor::tryFrom($color)) $clone->color = $instance;
            else throw new InvalidArgumentException(sprintf('Invalid background color %d', $color));
        } else $clone->color = $color;
        return $clone;
    }

    /**
     * Removes background color
     * @return static
     */
    public function withoutBackground(): static {
        $clone = clone $this;
        $clone->background = null;
        return $clone;
    }

    /**
     * Set Formats
     *
     * @param Format|int $formats
     * @return static
     * @throws InvalidArgumentException
     */
    public function withFormats(Format|int ...$formats): static {

        $clone = clone $this;

        $result = [];

        foreach ($formats as $format) {

            if (is_int($format)) {
                if ($instance = Format::tryFrom($format)) {
                    $result[] = $instance;
                } else throw new InvalidArgumentException(sprintf('Invalid format %d', $format));
            } else $result[] = $format;
        }

        $clone->formats = $result;
        return $clone;
    }

    /**
     * Adds formats
     *
     * @param Format|int $formats
     * @return static
     * @throws InvalidArgumentException
     */
    public function withAddedFormats(Format|int ...$formats): static {
        $clone = clone $this;

        $result = $clone->formats;
        foreach ($formats as $format) {

            if (is_int($format)) {
                if ($instance = Format::tryFrom($format)) {
                    $result[] = $instance;
                } else throw new InvalidArgumentException(sprintf('Invalid format %d', $format));
            } else $result[] = $format;
        }
        $clone->formats = $result;
        return $clone;
    }

    /**
     * Removes formats
     *
     * @return static
     */
    public function withoutFormat(): static {
        $clone = clone $this;
        $clone->formats = [];
        return $clone;
    }

}

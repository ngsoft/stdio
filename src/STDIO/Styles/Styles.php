<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use ArrayAccess,
    Countable,
    IteratorAggregate;
use NGSOFT\{
    Facades\Terminal, STDIO\Enums\BackgroundColor, STDIO\Enums\Color, STDIO\Enums\Format, STDIO\Formatters\Tag, STDIO\Outputs\Output, STDIO\Utils\Utils
};
use OutOfBoundsException,
    Traversable,
    ValueError;
use function get_debug_type,
             NGSOFT\Tools\map,
             preg_test;

class Styles implements ArrayAccess, IteratorAggregate, Countable
{

    protected const FORMATS_ENUMS = [Format::class, Color::class, BackgroundColor::class];

    public readonly bool $colors;

    /** @var Style[] */
    protected array $styles = [];
    protected array $formats = [];

    public function __construct(
            bool $colors = null
    )
    {
        $colors ??= Terminal::supportsColors();

        $this->colors = $colors;
        $this->buildStyles();
    }

    public function getStyles(): array
    {
        return $this->styles;
    }

    public function getFormats(): array
    {
        return $this->formats;
    }

    /**
     * Displays current styles to the output
     */
    public function displayStyles(Output $output): void
    {

        /** @var Style $style */
        foreach ($this as $style) {
            $output->writeln($style->format($style->getLabel(), $this->colors));
        }
    }

    /**
     * Add styles
     */
    public function addStyle(Style ...$styles): void
    {
        foreach ($styles as $style) {
            $this->styles[$style->getLabel()] = $style;
        }
    }

    /**
     * Create a style
     */
    public function createStyle(string $label, Format|Color|BackgroundColor|HexColor|BrightColor ...$styles): Style
    {
        static $cache = [];
        return $cache[$label] ??= (new Style($label))->setStyles(...$styles);
    }

    /**
     * Create a style using formats delimited by whitespaces
     */
    public function createStyleFromString(string $label): Style
    {


        if (isset($this[$label])) {
            return $this[$label];
        }


        if ( ! preg_test('#[\h\v]+#', $label) && str_contains($label, ';')) {
            $style = $this->createStyleFromAttributes(Tag::getTagAttributesFromCode($label), $label);
            if (count($style->getStyles())) {
                $this->addStyle($style);
            }
            return $style;
        }

        $formats = [];
        foreach (preg_split('#[\h\v]+#', $label) as $name) {

            if (isset($this[$name])) {
                $formats = array_merge($formats, $this[$name]->getStyles());
            } elseif (Utils::isHexColor($name)) {
                $formats[] = new HexColor($name);
            } elseif (Utils::isRGBColor($name)) {
                $formats[] = new RGBColor($name);
            }
        }

        if (count($formats)) {

            $this->addStyle(
                    $style = $this->createStyle($label, ...$formats)
            );

            return $style;
        }

        return new Style($label);
    }

    /**
     * Create style using tag attributes
     */
    public function createStyleFromAttributes(array $attributes, string $label = ''): Style
    {

        $availableFormats = &$this->formats;

        $formats = [];

        foreach ($attributes as $key => $val) {

            if (empty($val)) {
                if (isset($this[$key])) {
                    $formats = array_merge($formats, $this[$key]->getStyles());
                } elseif (Utils::isHexColor($key)) {
                    $formats[] = new HexColor($key, false, isset($attributes['grayscale']) || isset($attributes['gs']));
                } elseif (Utils::isRGBColor($key)) {
                    $formats[] = new RGBColor($key, false, isset($attributes['grayscale']) || isset($attributes['gs']));
                }
                continue;
            }


            if ($key === 'options' && count($val)) {
                $val = preg_split('#,+#', $val[0]);
            }

            foreach ($val as $format) {
                $format = strtolower($format);

                if (isset($availableFormats[$key][$format])) {
                    $formats[] = $availableFormats[$key][$format];
                    continue;
                }

                if (Utils::isHexColor($format) && in_array($key, ['fg', 'bg'])) {
                    $formats[] = new HexColor($format, $key === 'bg', isset($attributes['grayscale']) || isset($attributes['gs']));
                } elseif (Utils::isRGBColor($format) && in_array($key, ['fg', 'bg'])) {
                    $formats[] = new RGBColor($format, $key === 'bg', isset($attributes['grayscale']) || isset($attributes['gs']));
                }
            }
        }
        return (new Style($label))->setStyles(...$formats);
    }

    /** {@inheritdoc} */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->styles[$offset]);
    }

    /** {@inheritdoc} */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->styles[$offset] ?? null;
    }

    /** {@inheritdoc} */
    public function offsetSet(mixed $offset, mixed $value): void
    {

        if (is_null($offset) && $value instanceof Style && ! empty($value->getLabel())) {
            $offset = $value->getLabel();
        }


        if ( ! is_string($offset)) {
            throw new OutOfBoundsException(sprintf('Offset of type string expected, %s given.', get_debug_type($offset)));
        }

        if ( ! ($value instanceof Style)) {
            throw new ValueError(
                            sprintf(
                                    "Invalid type for value %s['%s']: %s expected, %s given",
                                    get_class($this), $offset, Style::class, get_debug_type($value)
                            )
            );
        }


        $this->styles[$offset] = $value;
    }

    /** {@inheritdoc} */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->styles[$offset]);
    }

    /** {@inheritdoc} */
    public function count(): int
    {
        return count($this->styles);
    }

    /** {@inheritdoc} */
    public function getIterator(): Traversable
    {
        yield from $this->styles;
    }

    protected function buildStyles(): void
    {
        static $cache = [], $formats = [], $custom = [
            'magenta' => [Color::PURPLE],
            'bg:magenta' => [BackgroundColor::PURPLE],
            'bg:white' => [BackgroundColor::GRAY],
            'emergency' => [Color::YELLOW, BackgroundColor::RED, Format::BOLD],
            'alert' => [Color::RED, Format::BOLD],
            'bg:alert' => [Color::GRAY, BackgroundColor::RED, Format::BOLD],
            'critical' => [Color::RED, Format::BOLD],
            'bg:critical' => [Color::GRAY, BackgroundColor::RED, Format::BOLD],
            'error' => [Color::RED],
            'bg:error' => [Color::GRAY, BackgroundColor::RED],
            'warning' => [Color::YELLOW],
            'bg:warning' => [Color::BLACK, BackgroundColor::YELLOW],
            'notice' => [Color::CYAN],
            'bg:notice' => [Color::GRAY, BackgroundColor::CYAN],
            'info' => [Color::GREEN],
            'success' => [Color::GREEN, Format::BOLD],
            'bg:success' => [Color::GRAY, BackgroundColor::GREEN, Format::BOLD],
            'bg:info' => [Color::GRAY, BackgroundColor::GREEN],
            'debug' => [Color::PURPLE],
            'bg:debug' => [BackgroundColor::PURPLE, Color::GRAY],
            'comment' => [Color::YELLOW],
            'whisper' => [Color::GRAY, Format::DIM],
            'shout' => [Color::RED, Format::BOLD],
        ];

        if (empty($cache)) {
            foreach (static::FORMATS_ENUMS as $enum) {
                foreach ($enum::cases() as $format) {
                    $cache[$format->getTag()] = $this->createStyle($format->getTag(), $format);
                    $prop = $format->getTagAttribute();
                    $formats[$prop] ??= [];
                    $formats[$prop][$format->getFormatName()] = $format;

                    if ( ! ($format instanceof Format) && $format->getName() !== 'DEFAULT') {

                        $bright = new BrightColor($format);
                        $cache[$bright->getTag()] = $this->createStyle($bright->getTag(), $bright);
                        $formats[$prop][$bright->getFormatName()] = $bright;
                    }
                }
            }
            foreach ($custom as $label => $styles) {
                $cache[$label] = $this->createStyle($label, ...$styles);
            }
        }


        $this->formats = $formats;
        $this->styles = $cache;
    }

    public function __debugInfo(): array
    {
        return map(fn($style, $label) => $style->format($label, $this->colors), $this->styles);
    }

}

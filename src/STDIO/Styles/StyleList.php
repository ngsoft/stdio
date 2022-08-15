<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use ArrayAccess,
    Countable,
    IteratorAggregate;
use NGSOFT\{
    Facades\Terminal, STDIO\Enums\BackgroundColor, STDIO\Enums\Color, STDIO\Enums\Format, STDIO\Formatters\NullFormatter, STDIO\Outputs\Output, STDIO\Utils\Utils
};
use OutOfBoundsException,
    Stringable,
    Traversable,
    ValueError;
use function get_debug_type,
             mb_strtolower,
             preg_exec;

class StyleList implements ArrayAccess, IteratorAggregate, Countable
{

    protected const FORMATS_ENUMS = [Format::class, Color::class, BackgroundColor::class];
    protected const FORMATS_CUSTOM = [
        ['magenta', Color::PURPLE],
        ['bg:magenta', BackgroundColor::PURPLE],
        ['bg:white', BackgroundColor::GRAY],
        ['emergency', Color::YELLOW, BackgroundColor::RED, Format::BOLD],
        ['alert', Color::RED, Format::BOLD],
        ['bg:alert', Color::GRAY, BackgroundColor::RED, Format::BOLD],
        ['critical', Color::RED, Format::BOLD],
        ['bg:critical', Color::GRAY, BackgroundColor::RED, Format::BOLD],
        ['error', Color::RED],
        ['bg:error', Color::GRAY, BackgroundColor::RED],
        ['warning', Color::YELLOW],
        ['bg:warning', Color::BLACK, BackgroundColor::YELLOW],
        ['notice', Color::CYAN],
        ['bg:notice', Color::GRAY, BackgroundColor::CYAN],
        ['info', Color::GREEN],
        ['success', Color::GREEN, Format::BOLD],
        ['bg:success', Color::GRAY, BackgroundColor::GREEN, Format::BOLD],
        ['bg:info', Color::GRAY, BackgroundColor::GREEN],
        ['debug', Color::PURPLE],
        ['bg:debug', BackgroundColor::PURPLE, Color::GRAY],
        ['comment', Color::YELLOW],
        ['whisper', Color::GRAY, Format::DIM],
        ['shout', Color::RED, Format::BOLD],
    ];

    /** @var Style[] */
    protected static $_styles = [];
    protected static $_formats = [];
    protected bool $colors;

    /** @var Style[] */
    protected array $styles = [];

    public function __construct(
            bool $forceColorSupport = null
    )
    {
        $this->colors = $forceColorSupport ?? Terminal::supportsColors();
        self::createDefaultStyles();
    }

    public function getFormats(): array
    {
        return self::$_formats;
    }

    /**
     * Format style using the style string
     */
    public function format(string|Stringable|array $messages, string $styleString = ''): string
    {
        return $this->createFromStyleString($styleString)->format($messages);
    }

    /**
     * Display Styles to the output
     */
    public function dumpStyles(Output $output = null)
    {

        $output ??= new Output(new NullFormatter());

        foreach ($this as $style) {
            $output->writeln($style->format($style->getLabel()));
        }
    }

    /**
     * Add style if not exists
     */
    public function register(Style $style, string $label = null): void
    {

        $label ??= $style->getLabel();

        if (empty($label) || $style->isEmpty()) {
            return;
        }

        if ( ! $this->offsetExists($style->getLabel())) {
            $this->offsetSet($style->getLabel(), $style);
        }
    }

    /**
     * Creates a style
     */
    public function create(string $label, Format|Color|BackgroundColor|HexColor|BrightColor ...$styles): Style
    {
        $this->register($style = Style::createFrom($label, ...$styles)->withColorSupport($this->colors));
        return $style;
    }

    public function createFromStyleString(string $string): Style
    {
        if (isset($this[$string])) {
            return $this[$string];
        }

        return $this->createStyleFromParams($this->getParamsFromStyleString($string), $string);
    }

    public function createStyleFromParams(array $params, string $label = ''): Style
    {
        $style = Style::createEmpty()->withLabel($label)->withColorSupport($this->colors);

        if ($params) {

            $isGray = isset($params['grayscale']) || isset($params['gs']);

            foreach ($params as $key => $value) {

                $isBG = $key === 'bg';

                if (empty($value)) {
                    if (isset($this[$key])) {
                        $style = $style->withAddedStyle($this[$key]);
                    } elseif (Utils::isHexColor($key)) {
                        $style = $style->withAddedFormats(new HexColor($key, $isBG, $isGray));
                    } elseif (Utils::isRGBColor($key)) {
                        $style = $style->withAddedFormats(new RGBColor($key, $isBG, $isGray));
                    }
                    continue;
                }

                if ($key === 'options') {
                    $value = preg_split('#[;,]+#', $value);
                } else { $value = preg_split('#;+#', $value); }

                foreach ($value as $val) {
                    $val = mb_strtolower($val);

                    if (isset(self::$_formats[$key][$val])) {


                        $style = $style->withAddedFormats(self::$_formats[$key][$val]);
                    } elseif (Utils::isHexColor($val)) {
                        $style = $style->withAddedFormats(new HexColor($val, $isBG, $isGray));
                    } elseif (Utils::isRGBColor($val)) {
                        $style = $style->withAddedFormats(new RGBColor($val, $isBG, $isGray));
                    }
                }
            }
        }

        $this->register($style);

        return $style;
    }

    /**
     * Parse Style Param string
     *
     * @param string $string
     * @return array<string, string>
     */
    public function getParamsFromStyleString(string $string): array
    {
        static $cache = [];

        $string = trim(trim($string), ',;');

        if (empty($string)) {
            return [];
        }

        if (isset($cache[$string])) {
            return $cache[$string];
        }

        $cache[$string] = [];

        $params = &$cache[$string];

        foreach (preg_split('#[;\h\v]+#', $string) as $param) {

            @list(, $key, $val) = preg_exec('#([^=]+)(?:=(.+))?#', $param);

            if (isset($key)) {
                $key = mb_strtolower(trim($key));
                if (empty($key)) {
                    continue;
                }
                $params[$key] ??= '';
                if (isset($val)) {
                    $params[$key] .= ';' . trim($val);
                    $params[$key] = ltrim($params[$key], ';');
                }
            }
        }
        return $params;
    }

    public function offsetExists(mixed $offset): bool
    {
        return isset(self::$_styles[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {

        if ( ! $this->offsetExists($offset)) {
            return null;
        }

        return $this->styles[$offset] ??= self::$_styles[$offset]->withColorSupport($this->colors);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
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

        unset($this->styles[$offset]);

        self::$_styles[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void
    {
        unset(self::$_styles[$offset], $this->styles[$offset]);
    }

    public function count(): int
    {
        return count(self::$_styles);
    }

    public function getIterator(): Traversable
    {
        $labels = array_keys(self::$_styles);

        foreach ($labels as $label) {
            yield $label => $this->offsetGet($label);
        }
    }

    public function __debugInfo(): array
    {

        $result = [];
        foreach ($this as $label => $style) {

            $result[$label] = $style->format($label);
        }

        return $result;
    }

    protected static function createDefaultStyles(): void
    {

        if (empty(self::$_styles)) {
            $styles = &self::$_styles;
            $formats = &self::$_formats;

            /** @var Color $format */
            foreach (self::FORMATS_ENUMS as $enum) {
                foreach ($enum::cases() as $format) {
                    $prop = $format->getTagAttribute();
                    $formats[$prop] ??= [];
                    $formats[$prop][$format->getFormatName()] = $format;

                    $styles[$format->getTag()] = Style::createFrom($format->getTag(), $format);

                    if ( ! $format->is(Color::DEFAULT, BackgroundColor::DEFAULT, ...Format::cases())) {
                        $bright = new BrightColor($format);
                        $formats[$prop][$bright->getFormatName()] = $bright;
                        $styles[$bright->getTag()] = Style::createFrom($bright->getTag(), $bright);
                    }
                }
            }


            foreach (self::FORMATS_CUSTOM as $args) {
                $styles[$args[0]] = Style::createFrom(...$args);
            }
        }
    }

}

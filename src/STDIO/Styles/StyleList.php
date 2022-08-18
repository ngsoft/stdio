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
    protected const FORMATS_ALIASES = [
        'purple' => 'magenta',
        'bg:purple' => 'bg:magenta',
        'purple:bright' => 'magenta:bright',
        'bg:purple:bright' => 'bg:magenta:bright',
        'gray:bright' => 'white',
        'cyan' => 'aqua',
        'bg:cyan' => 'bg:aqua',
        'cyan:bright' => 'aqua:bright',
        'bg:cyan:bright' => 'bg:aqua:bright',
    ];
    protected const FORMATS_CUSTOM = [
        ['href', Color::CYAN, Format::UNDERLINE],
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
    protected const FORMATS_COLOR = [
        Color256::class,
        HexColor::class,
        RGBColor::class,
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

        if ( ! $this->offsetExists($label)) {
            $this->offsetSet($label, $style);
        }
    }

    /**
     * Creates a style
     */
    public function create(string $label, Format|Color|BackgroundColor|CustomColor ...$styles): Style
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
            $types = array_keys(self::$_formats);
            $hasParams = count($types) !== count(array_diff($types, array_keys($params)));

            foreach ($params as $key => $value) {

                $isBG = $key === 'bg';

                if (empty($value)) {
                    if (isset($this[$key])) {
                        $style = $style->withAddedStyle($this[$key]);
                    } elseif (Utils::is256Color($key)) {
                        $style = $style->withAddedFormats(new Color256($key, $isBG));
                    } elseif (Utils::isHexColor($key)) {
                        $style = $style->withAddedFormats(new HexColor($key, $isBG, $isGray));
                    } elseif (Utils::isRGBColor($key)) {
                        $style = $style->withAddedFormats(new RGBColor($key, $isBG, $isGray));
                    }
                    continue;
                }


                if (isset($this[$key]) && ! $hasParams) {
                    $style = $style->withAddedStyle($this[$key]);
                    continue;
                } elseif ( ! isset(self::$_formats[$key])) {
                    continue;
                }

                $isOptions = false;

                if ($key === 'options') {
                    $value = preg_split('#[;,]+#', $value);
                    $isOptions = true;
                } else { $value = preg_split('#;+#', $value); }

                foreach ($value as $val) {
                    $val = mb_strtolower($val);
                    if (isset(self::$_formats[$key][$val])) {
                        $style = $style->withAddedFormats(self::$_formats[$key][$val]);
                    } elseif (Utils::is256Color($val)) {
                        $style = $style->withAddedFormats(new Color256($val, $isBG));
                    } elseif (Utils::isHexColor($val) && ! $isOptions) {
                        $style = $style->withAddedFormats(new HexColor($val, $isBG, $isGray));
                    } elseif (Utils::isRGBColor($val) && ! $isOptions) {
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
            $aliases = self::FORMATS_ALIASES;

            /** @var Color $format */
            foreach (self::FORMATS_ENUMS as $enum) {
                foreach ($enum::cases() as $format) {
                    $prop = $format->getTagAttribute();
                    $name = $format->getFormatName();
                    $tag = $format->getTag();

                    $formats[$prop] ??= [];
                    $formats[$prop][$name] = $format;
                    $styles[$tag] = Style::createFrom($tag, $format);

                    if (isset($aliases[$name])) {
                        $formats[$prop][$aliases[$name]] = $format;
                    }

                    if (isset($aliases[$tag])) {
                        $styles[$aliases[$tag]] = Style::createFrom($aliases[$tag], $format);
                    }

                    if ( ! $format->is(Color::DEFAULT, BackgroundColor::DEFAULT, ...Format::cases())) {
                        $bright = new BrightColor($format);

                        $name = $bright->getFormatName();
                        $tag = $bright->getTag();

                        $formats[$prop][$name] = $bright;
                        $styles[$tag] = Style::createFrom($tag, $bright);

                        if (isset($aliases[$name])) {
                            $formats[$prop][$aliases[$name]] = $bright;
                        }
                        if (isset($aliases[$tag])) {
                            $styles[$aliases[$tag]] = Style::createFrom($aliases[$tag], $bright);
                        }
                    }
                }
            }

            foreach (self::FORMATS_CUSTOM as $args) {
                $styles[$args[0]] = Style::createFrom(...$args);
            }
        }
    }

}

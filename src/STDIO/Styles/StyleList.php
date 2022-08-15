<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use ArrayAccess,
    Countable,
    IteratorAggregate;
use NGSOFT\{
    Facades\Terminal, STDIO\Enums\BackgroundColor, STDIO\Enums\Color, STDIO\Enums\Format, STDIO\Utils\Utils
};
use OutOfBoundsException,
    Traversable,
    ValueError;
use function get_debug_type,
             mb_strtolower,
             preg_exec;

class StyleList implements ArrayAccess, IteratorAggregate, Countable
{

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
        $this->colors = $forceColorSupport ??= Terminal::supportsColors();
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
        $this->register($style = Style::createFrom($label, ...$styles));
        return $style;
    }

    public function createFromStyleString(string $string): Style
    {
        if (isset($this[$string])) {
            return $this[$string];
        }


        $style = Style::createEmpty()->withLabel($string);

        if ($params = $this->getParamsFromStyleString($string)) {

            $isGray = isset($params['grayscale']) || isset($params['gs']);
            $isBG = $key = 'bg';

            foreach ($params as $key => $value) {
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
                        $style = $style->withAddedFormats($_formats[$key][$val]);
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
        $string = trim(trim($string), ',;');

        if (empty($string)) {
            return [];
        }
        $params = [];

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

}

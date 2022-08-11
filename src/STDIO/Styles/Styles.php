<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use ArrayAccess,
    Countable,
    IteratorAggregate;
use NGSOFT\{
    Facades\Terminal, STDIO\Enums\BackgroundColor, STDIO\Enums\Color, STDIO\Enums\Format, STDIO\Outputs\Output
};
use OutOfBoundsException,
    Traversable,
    ValueError;
use function get_debug_type;

class Styles implements ArrayAccess, IteratorAggregate, Countable
{

    /** @var Style[] */
    protected array $styles = [];

    public function __construct(
            protected ?bool $colors = null
    )
    {
        $this->colors ??= Terminal::supportsColors();
        $this->buildStyles();
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
    public function createStyle(string $label, Format|Color|BackgroundColor ...$styles): Style
    {
        $style = new Style($label);
        return $style->setStyles(...$styles);
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

        if ( ! is_string($offset)) {
            throw new OutOfBoundsException(sprintf('Offset of type string expected, %s given.', get_debug_type($offset)));
        }

        if ( ! ($value instanceof Style)) {
            throw new ValueError(
                            sprintf(
                                    "Invalid type for value %s['%s']: %s expected, %s given",
                                    get_class($this), (string) $offset, Style::class, get_debug_type($value)
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
        static $cache = [], $custom = [
            'emergency' => [Color::YELLOW, BackgroundColor::RED, Format::BOLD],
            'alert' => [Color::RED, Format::BOLD],
            'critical' => [Color::RED, Format::BOLD],
            'error' => [Color::RED],
            'warning' => [Color::YELLOW],
            'notice' => [Color::CYAN],
            'info' => [Color::GREEN],
            'debug' => [Color::PURPLE],
            'comment' => [Color::YELLOW],
            'whisper' => [Color::GRAY, Format::DIM],
            'shout' => [Color::RED, Format::BOLD],
        ];

        if (empty($cache)) {

            foreach ([Format::class, Color::class, BackgroundColor::class] as $enum) {
                $aliases[$enum] = [];
                foreach ($enum::cases() as $format) {
                    $aliases[$enum] [strtolower($format->getName())] = $format->getTag();
                    $cache[$format->getTag()] = $this->createStyle($format->getTag(), $format);
                }
            }


            foreach ($custom as $label => $styles) {
                $cache[$label] = $this->createStyle($label, ...$styles);
            }
        }


        $this->styles = $cache;
    }

}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO;

use ArrayAccess,
    Countable,
    InvalidArgumentException,
    IteratorAggregate;
use NGSOFT\STDIO\{
    Outputs\Output, Styles\Style, Enums\BackgroundColor, Enums\BrightBackgroundColor, Enums\BrightColor, Enums\Color, Enums\Format
};
use Traversable;

class StyleSheet implements ArrayAccess, IteratorAggregate, Countable {

    protected array $styles = [];
    protected array $fg = [];
    protected array $bg = [];
    protected bool $colorSupport;

    public function __construct(bool $colorSupport = null, array $styles = []) {
        $this->colorSupport = is_bool($colorSupport) ? $colorSupport : Terminal::create()->colors;
        if (empty($styles)) $this->buildStyles();
        else $this->styles = $styles;
    }

    /**
     * Displays current styles to the output
     *
     * @param Output $output
     * @return void
     */
    public function displayStyles(Output $output): void {

        /** @var Style $style */
        foreach ($this as $style) {
            $output->writeln($style->format($style->getLabel()));
        }
    }

    /**
     * Adds Custom style
     *
     * @param string $label
     * @param Color|Format ...$formats
     * @return static
     */
    public function addStyle(string $label, Color|Format|int ...$formats): static {
        $this->styles[$label] = $this->createStyle($label, ...$formats);
        return $this;
    }

    protected function createStyle(string $label, Color|Format|int ...$formats): Style {

        $style = new Style($this->colorSupport);

        $style = $style->withLabel($label);

        foreach ($formats as $format) {
            if (is_int($format)) {
                if (
                        $implFormat = Color::tryFrom($format) ??
                        BrightColor::tryFrom($format) ??
                        BackgroundColor::tryFrom($format) ??
                        BrightBackgroundColor::tryFrom($format) ??
                        Format::tryFrom($format)
                ) {
                    $format = $implFormat;
                } else throw new InvalidArgumentException(sprintf('Invalid format %d.', $format));
            }


            if ($format instanceof BackgroundColor) {
                $style = $style->withBackground($format);
            } elseif ($format instanceof Color) {
                $style = $style->withColor($format);
            } else $style = $style->withFormats($format);
        }

        return $style;
    }

    protected function buildStyles(): void {
        static
        $cache = [],
        $custom,
        $prefixes = [
            Color::class => '',
            BrightColor::class => 'bright-',
            BackgroundColor::class => 'bg-',
            BrightBackgroundColor::class => 'bg-bright-',
            Format::class => ''
        ];

        $custom = $custom ?? [
            'emergency' => [Color::YELLOW(), BackgroundColor::RED(), Format::BOLD()],
            'alert' => [Color::RED(), Format::BOLD()],
            'critical' => [Color::RED(), Format::BOLD()],
            'error' => [Color::RED()],
            'warning' => [Color::YELLOW()],
            'notice' => [Color::CYAN()],
            'info' => [Color::CYAN()],
            'debug' => [Color::PURPLE()],
            'comment' => [Color::YELLOW()],
            'whisper' => [Color::WHITE(), Format::DIM()],
            'shout' => [Color::RED(), Format::BOLD()],
        ];

        $key = (int) $this->colorSupport;

        // create cache for color support(false, true)
        if (!isset($cache[$key])) {

            $cache[$key] = [
                'styles' => [],
                'fg' => [],
                'bg' => []
            ];

            foreach ($prefixes as $className => $prefix) {



                foreach ($className::cases() as $format) {
                    $cleanName = strtolower($format->name);
                    if ($format instanceof BrightBackgroundColor || $format instanceof BrightColor) $cleanName = "b$cleanName";
                    if ($format instanceof BackgroundColor) $cache[$key]['bg'][$cleanName] = $this->createStyle($cleanName, $format);
                    elseif ($format instanceof Color) $cache[$key]['fg'][$cleanName] = $this->createStyle($cleanName, $format);

                    $label = $prefix . strtolower($format->name);
                    $cache[$key]['styles'][$label] = $this->createStyle($label, $format);
                }
            }

            foreach ($custom as $label => $params) {
                $cache[$key]['styles'][$label] = $this->createStyle($label, ...$params);
            }
        }

        $this->styles = $cache[$key]['styles'];

        $this->bg = $cache[$key]['bg'];
        $this->fg = $cache[$key]['fg'];
    }

    public function offsetExists(mixed $offset): bool {

        return isset($this->styles[$offset]);
    }

    public function offsetGet(mixed $offset): mixed {
        return $this->styles[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void {
        if ($value instanceof Style) $this->styles[$offset] = $value;
    }

    public function offsetUnset(mixed $offset): void {

        unset($this->styles[$offset]);
    }

    public function count(): int {

        return count($this->styles);
    }

    /**
     *
     * @return Traversable<string,Style>
     */
    public function getIterator(): Traversable {

        foreach ($this->styles as $label => $style) {
            yield $label => $style;
        }
    }

}

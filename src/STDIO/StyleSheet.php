<?php

declare(strict_types=1);

namespace NGSOFT\STDIO;

use ArrayAccess,
    Countable,
    IteratorAggregate;
use NGSOFT\STDIO\{
    Outputs\Output, Styles\Style, Values\BackgroundColor, Values\BrightBackgroundColor, Values\BrightColor, Values\Color, Values\Format
};
use Traversable;

class StyleSheet implements ArrayAccess, IteratorAggregate, Countable {

    protected array $styles = [];
    protected bool $colorSupport;

    public function __construct(bool $colorSupport = null, array $styles = []) {
        if (!is_bool($colorSupport)) $colorSupport = Terminal::create()->colors;
        $this->colorSupport = $colorSupport;
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
    public function addStyle(string $label, Color|Format ...$formats): static {
        $this->styles[$label] = $this->createStyle($label, ...$formats);
        return $this;
    }

    protected function createStyle(string $label, Color|Format ...$formats): Style {

        $style = new Style($this->colorSupport);
        $style = $style->withLabel($label);

        foreach ($formats as $format) {
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
        if (empty($cache)) {
            $colorSupport = $this->colorSupport;

            foreach ($prefixes as $className => $prefix) {
                foreach ($className::getValues() as $format) {
                    $label = $prefix . strtolower($format->getLabel());
                    $cache[$label] = $this->createStyle($label, $format);
                }
            }

            foreach ($custom as $label => $params) {
                $cache[$label] = $this->createStyle($label, ...$params);
            }
        }

        $this->styles = $cache;
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

    public function getIterator(): Traversable {

        foreach ($this->styles as $label => $style) {
            yield $label => $style;
        }
    }

}

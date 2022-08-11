<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use NGSOFT\{
    Enums\Enum, Facades\Terminal, STDIO\Enums\BackgroundColor, STDIO\Enums\Color, STDIO\Enums\Format, STDIO\Outputs\Output
};

class Styles
{

    /** @var Style[] */
    protected array $styles = [];
    protected array $aliases = [];

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

    protected function buildStyles(): void
    {
        static $cache = [], $aliases = [], $custom = [
            'emergency' => [Color::YELLOW, BackgroundColor::RED, Format::BOLD],
            'alert' => [Color::RED, Format::BOLD],
            'critical' => [Color::RED, Format::BOLD],
            'error' => [Color::RED],
            'warning' => [Color::YELLOW],
            'notice' => [Color::CYAN],
            'info' => [Color::CYAN],
            'debug' => [Color::PURPLE],
            'comment' => [Color::YELLOW],
            'whisper' => [Color::WHITE, Format::DIM],
            'shout' => [Color::RED, Format::BOLD],
        ];

        if (empty($cache)) {

            /** @var Enum $enum */
            /** @var Color|BackgroundColor|Format $format */
            foreach ([Format::class, Color::class, BackgroundColor::class] as $enum) {

                $aliases[$enum] = [];

                foreach ($enum::cases() as $format) {
                    $aliases[$enum] [strtolower($format->getName())] = $format->getTag();
                    $cache[$format->getTag()] = $this->createStyle($format->getTag(), $format);
                }
            }
        }


        $this->styles = $cache;
        $this->aliases = $aliases;
    }

}

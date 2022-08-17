<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

use NGSOFT\STDIO\{
    Enums\Ansi, Outputs\Output, Styles\StyleList, Utils\Utils
};

class Spinner extends Helper
{

    public const THEME_DEFAULT = ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏',];
    public const THEME_CLASSIC = ['-', '\\', '|', '/',];
    public const THEME_BAR = ["▰▱▱▱▱▱▱", "▰▰▱▱▱▱▱", "▰▰▰▱▱▱▱", "▰▰▰▰▱▱▱", "▰▰▰▰▰▱▱", "▰▰▰▰▰▰▱", "▰▰▰▰▰▰▰", "▰▱▱▱▱▱▱",];
    protected const COLORS = [
        196, 196, 202, 202, 208, 208, 214, 214, 220, 220, 226, 226, 190, 190,
        154, 154, 118, 118, 82, 82, 46, 46, 47, 47, 48, 48, 49, 49, 50, 50,
        51, 51, 45, 45, 39, 39, 33, 33, 27, 27, 56, 56, 57, 57, 93, 93, 129, 129,
        165, 165, 201, 201, 200, 200, 199, 199, 198, 198, 197, 197,
    ];

    protected array $position = [];
    protected bool $cursorEnabled = true;
    protected bool $colors = true;
    protected int $index = -1;
    protected array $theme = [];
    protected string $label = '';
    protected float $interval = .1;

    public function __construct(?StyleList $styles = null)
    {
        parent::__construct($styles);
    }

    public function getTheme(): array
    {
        return $this->theme;
    }

    public function setTheme(array $theme): void
    {

        if ( ! empty($theme)) {
            $this->theme = $theme;
            $this->index = -1;
        }
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): void
    {
        $this->label = $label;
    }

    protected function update(): void
    {
        static $output;
        $output ??= new Output();

        if (empty($this->position)) {
            $this->position = $output->getCursor()->getPosition($enabled);
            $this->cursorEnabled = $enabled;

            $this->colors = Utils::getNumColorSupport() > 255;
            if (empty($this->theme)) {
                $this->setTheme($this->colors ? self::THEME_DEFAULT : self::THEME_CLASSIC);
            }
        }

        $this->index ++;
        $index = $this->index % count($this->theme);

        $char = $this->theme[$index];

        $style = $this->getStyle();
        if ($style->isEmpty()) {
            $indexColor = $this->index % count(self::COLORS);
            $style = $this->styles->createFromStyleString('c' . self::COLORS[$indexColor]);
        }

        if ($this->cursorEnabled) {
            $this->write(sprintf(Ansi::CURSOR_POS . Ansi::CLEAR_END_LINE, ...array_reverse($this->position)));
        } else { $this->write(Ansi::CLEAR_LINE . "\r"); }

        $this->write($style->format($char));

        if ( ! empty($this->label)) {
            $this->write(' ' . $this->label);
        }
    }

    public function __toString(): string
    {
        $this->update();
        return implode('', $this->buffer->pull());
    }

}

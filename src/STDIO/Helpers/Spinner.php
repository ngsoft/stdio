<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

use NGSOFT\STDIO\{
    Enums\Ansi, Outputs\Cursor, Styles\StyleList, Utils\Utils
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
    protected const COLORS_CLASSIC = [
        1, 1,
        2, 2, 2, 2,
        3, 3, 3, 3,
        4, 4, 4, 4,
        5, 5, 5, 5,
        6, 6, 6, 6,
        7, 7, 7, 7,
        6, 6, 6, 6,
        5, 5, 5, 5,
        4, 4, 4, 4,
        3, 3, 3, 3,
        2, 2, 2, 2,
        1, 1,
    ];

    protected Cursor $cursor;
    protected array $position = [];
    protected int $index = -1;
    protected array $theme = [];
    protected array $colors = [];
    protected string $label = '';
    protected float $interval = .1;

    public function __construct(?StyleList $styles = null)
    {
        parent::__construct($styles);
        // use the buffer as output to pre write the ansi codes
        $this->cursor = new Cursor($this->buffer);
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
        $cursor = $this->cursor;

        if (empty($this->position)) {
            $this->position = $cursor->getPosition();
            $colors = Utils::getNumColorSupport() > 255;

            if (empty($this->theme)) {
                $this->setTheme($colors ? self::THEME_DEFAULT : self::THEME_CLASSIC);
            }
            if (empty($this->colors)) {
                $this->colors = $colors ? self::COLORS : self::COLORS_CLASSIC;
            }
        }

        $this->index ++;
        $index = $this->index % count($this->theme);

        $char = $this->theme[$index];

        $style = $this->getStyle();
        if ($style->isEmpty()) {
            $indexColor = $this->index % count($this->colors);
            $style = $this->styles->createFromStyleString('c' . $this->colors[$indexColor]);
        }

        if ($cursor->isCursorEnabled()) {
            list($x, $y) = $this->position;

            $cursor->setPosition($x, $y);
            $cursor->clearRight();
        } else {
            // position the cursor at the beginning of the line
            $this->write(Ansi::RESET);
            $cursor->col();
            $cursor->clearLine();
            // we must be on windows with a dummy vt that think that "\r" == "\n" (ie: netbeans run command)
            //$this->write("\r");
        }

        $label = $char . ' ';

        if ( ! empty($this->label)) {
            $label .= $this->label . ' ';
        }


        $this->write($style->format($label));
    }

    public function __toString(): string
    {
        $this->update();
        return (string) $this->buffer;
    }

}

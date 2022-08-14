<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use InvalidArgumentException,
    NGSOFT\STDIO\Utils\Utils;
use function preg_test;

/**
 * Converts Hex Color to ansi
 */
class HexColor
{

    public readonly string $name;
    protected string $value;

    public function __construct(
            string $name,
            public bool $isBackgroundColor = false
    )
    {
        $this->value = self::convertHexToAnsi($name, $isBackgroundColor);
        $this->name = '#' . ltrim($name, '#');
    }

    public static function isHexColor(string $color): bool
    {
        return preg_test('/^#?(?:[0-9A-F]{3}){1,2}$/i', $color);
    }

    /**
     * Convert hex color to ansi code
     */
    protected static function convertHexToAnsi(string $hexColor, bool $isBackgroundColor = false): string
    {

        static $mode;

        if ( ! self::isHexColor($hexColor)) {
            throw new InvalidArgumentException(sprintf('Invalid "%s" color.', $hexColor));
        }

        if ( ! $mode) {
            $mode = 'ansi';
            if (Utils::getNumColorSupport() > 256) {
                $mode = 'truecolor';
            } elseif (Utils::getNumColorSupport() == 256) {
                $mode = '256color';
            }
        }

        $color = ltrim($hexColor, '#');

        if (3 === strlen($color)) {
            $color = $color[0] . $color[0] . $color[1] . $color [1] . $color[2] . $color[2];
        }

        [$red, $green, $blue] = array_map(fn($hex) => intval($hex, 16), str_split($color, 2));

        if ($mode === '256color') {
            return sprintf('%d8;5;%d', $isBackgroundColor ? 4 : 3, self::degradeTo256($red, $green, $blue));
        } elseif ($mode === 'ansi') {
            return sprintf('%d%d', $isBackgroundColor ? 4 : 3, self::degradeToAnsi($red, $green, $blue));
        }

        return sprintf('%d8;2;%d;%d;%d', $isBackgroundColor ? 4 : 3, $red, $green, $blue);
    }

    protected static function degradeToGrayscale(int $red, int $green, int $blue): int
    {
        static $table = [8, 18, 28, 38, 48, 58, 68, 78, 88, 98, 108, 118, 128, 138, 148, 158, 168, 178, 188, 198, 208, 218, 228, 238];

        $max = max($red, $green, $blue);
        $min = min($red, $green, $blue);
        $middle = (int) floor((($max - $min) / 2) + $min);

        if ($middle > 238) {
            return 231;
        } elseif ($middle < 8) {
            return 16;
        }


        foreach ($table as $level => $intensity) {

            if ($middle < $intensity) {
                break;
            }
        }

        return 232 + $level;
    }

    /**
     * Find nearest 256 from table
     */
    protected static function degradeTo256(int $red, int $green, int $blue): int
    {
        static $table = [0, 95, 135, 175, 215, 255];

        $lRed = $lGreen = $lBlue = 0;

        foreach ($table as $level => $intensity) {

            if ($red >= $intensity) {
                $lRed = $level;
            }

            if ($green >= $intensity) {
                $lGreen = $level;
            }

            if ($blue >= $intensity) {
                $lBlue = $level;
            }
        }

        return 16 + (36 * $lRed) + (6 * $lGreen) + $lBlue;
    }

    protected static function degradeToAnsi(int $red, int $green, int $blue): int
    {
        return (int) (floor($red / 128) + (floor($green / 128) * 2) + (floor($blue / 128) * 4));
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getUnsetValue(): int
    {
        return $this->isBackgroundColor ? 49 : 39;
    }

}

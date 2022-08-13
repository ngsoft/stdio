<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use InvalidArgumentException;
use function preg_test;

/**
 * Converts Hex Color to ansi
 */
class HexColor
{

    public readonly string $name;
    protected array $value = [];

    public function __construct(
            string $name,
            public bool $isBackgroundColor = false
    )
    {
        $this->value = self::convertHexToAnsi($name);
        $this->name = '#' . ltrim($name, '#');
    }

    public static function isHexColor(string $color): bool
    {
        $len = strlen($color);
        if ($len !== 3 && $len !== 6) {
            return false;
        }
        return preg_test('/^#?[0-9A-F]+$/i', $color);
    }

    /**
     * Convert hex color to ansi code
     */
    protected static function convertHexToAnsi(string $hexColor, bool $isBackgroundColor = false): string
    {
        $color = ltrim('#', $hexColor);

        if (3 === strlen($color)) {
            $color = $color[0] . $color[0] . $color[1] . $color [1] . $color[2] . $color[2];
        }

        if (6 !== strlen($color) || ! ctype_xdigit($color)) {
            throw new InvalidArgumentException(sprintf('Invalid "%s" color.', $hexColor));
        }

        $color = hexdec($color);

        $red = ($color >> 16) & 255;
        $green = ($color >> 8) & 255;
        $blue = $color & 255;

        if ('truecolor' !== getenv('COLORTERM')) {
            return sprintf('%d%d', $isBackgroundColor ? 4 : 3, self::degradeToAnsi($red, $green, $blue));
        }

        return sprintf('%d8;2;%d;%d;%d', $isBackgroundColor ? 4 : 3, $red, $green, $blue);
    }

    protected static function degradeToAnsi(int $red, int $green, int $blue): string
    {

        if (0 === round(self::getSaturation($red, $green, $blue) / 50)) {
            return 0;
        }

        return int((round($blue / 255) << 2) | (round($green / 255) << 1) | round($red / 255));
    }

    protected static function getSaturation(int $red, int $green, int $blue): int
    {
        $red = $red / 255;
        $green = $green / 255;
        $blue = $blue / 255;
        $value = max($red, $green, $blue);

        if (0 === $diff = $value - min($red, $green, $blue)) {
            return 0;
        }

        return (int) $diff * 100 / $value;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getUnsetValue(): string
    {
        return $this->isBackgroundColor ? 49 : 39;
    }

}

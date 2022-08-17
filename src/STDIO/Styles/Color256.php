<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use InvalidArgumentException,
    NGSOFT\STDIO\Utils\Utils;
use function in_range;

class Color256 implements CustomColor
{

    protected int|string $color;

    public function __construct(
            int|string $color,
            protected bool $isBackgroundColor = false,
            protected string $name = ''
    )
    {
        static $table = [0, 95, 135, 175, 215, 255], $isSupported;

        $isSupported ??= Utils::getNumColorSupport() > 255;

        if ( ! is_int($color) && preg_match('#\d+#', $color, $matches)) {
            $color = $matches[0];
        }

        if ( ! is_int($color)) {
            throw new InvalidArgumentException(sprintf('Color %s is invalid.', $color));
        }


        if ( ! in_range($color, 0, 255)) {
            throw new InvalidArgumentException(sprintf('Color number %d is not in range [0-255].', $color));
        }

        if (empty($name)) {

            $name = '' . $color;
            if ($color < 100) {
                $name = '0' . $name;
            }

            if ($color < 10) {
                $name = '0' . $name;
            }

            $this->name = sprintf('c%s', $name);
        }

        $this->color = sprintf('%d8;5;%d', $isBackgroundColor ? 4 : 3, $color);

        if ( ! $isSupported) {
            //convert to ansi [30-37], [40-47]

            if ($color < 16) {
                $this->color = $color + 30;
                if ($isBackgroundColor) {
                    $this->color += 10;
                }
                // bright colors
                if ($color > 7) {
                    $this->color = sprintf('1;%d', $this->color - 8);
                }
            } elseif ($color > 231) {
                // grayscales to black / white
                $color = ($color - 231) / 24; // [0-24] / 24
                if ($color <= 0.5) {
                    $this->color = 30;
                } else { $this->color = 37; }

                if ($isBackgroundColor) {
                    $this->color += 10;
                }
            } else {

                // convert to r,g,b using table
                $color -= 16;
                $red = (int) floor($color / 36);
                $color -= $red * 36;
                $green = (int) floor($color / 6);
                $blue = $color - ($green * 6);

                // convert rgb to [30-37]
                $color = Utils::degradeToAnsi($table[$red], $table[$green], $table[$blue]) + 30;

                if ($isBackgroundColor) {
                    $color += 10;
                }
                $this->color = $color;
            }
        }
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUnsetValue(): int|string
    {
        return $this->isBackgroundColor ? 49 : 39;
    }

    public function getValue(): int|string
    {
        return $color;
    }

}

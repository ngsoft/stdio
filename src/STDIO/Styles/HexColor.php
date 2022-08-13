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


        $color = hexdec($color);

        if ($mode === '256color') {
            return sprintf('%d8;5;%d', $isBackgroundColor ? 4 : 3, self::degradeTo256($color));
        }


        $red = ($color >> 16) & 255;
        $green = ($color >> 8) & 255;
        $blue = $color & 255;

        if ($mode !== 'truecolor') {
            return sprintf('%d%d', $isBackgroundColor ? 4 : 3, self::degradeToAnsi($red, $green, $blue));
        }
        return sprintf('%d8;2;%d;%d;%d', $isBackgroundColor ? 4 : 3, $red, $green, $blue);
    }

    /**
     * Find nearest 256 from table
     */
    protected static function degradeTo256(int $color, bool $grayscale = false): int
    {

        /**
         * 256 color table from link converted to int
         * @link https://gist.github.com/ifonefox/6046257
         */
        static $table = [
            [0, 0], [0, 16], [95, 17], [128, 4],
            [135, 18], [175, 19], [215, 20], [255, 12],
            [255, 21], [24320, 22], [24415, 23], [24455, 24],
            [24495, 25], [24535, 26], [24575, 27], [32768, 2],
            [32896, 6], [34560, 28], [34655, 29], [34695, 30],
            [34735, 31], [34775, 32], [34815, 33], [44800, 34],
            [44895, 35], [44935, 36], [44975, 37], [45015, 38],
            [45055, 39], [55040, 40], [55135, 41], [55175, 42],
            [55215, 43], [55255, 44], [55295, 45], [65280, 10],
            [65280, 46], [65375, 47], [65415, 48], [65455, 49],
            [65495, 50], [65535, 14], [65535, 51], [526344, 232],
            [1184274, 233], [1842204, 234], [2500134, 235], [3158064, 236],
            [3815994, 237], [4473924, 238], [5131854, 239], [5789784, 240],
            [6225920, 52], [6226015, 53], [6226055, 54], [6226095, 55],
            [6226135, 56], [6226175, 57], [6250240, 58], [6250335, 59],
            [6250375, 60], [6250415, 61], [6250455, 62], [6250495, 63],
            [6260480, 64], [6260575, 65], [6260615, 66], [6260655, 67],
            [6260695, 68], [6260735, 69], [6270720, 70], [6270815, 71],
            [6270855, 72], [6270895, 73], [6270935, 74], [6270975, 75],
            [6280960, 76], [6281055, 77], [6281095, 78], [6281135, 79],
            [6281175, 80], [6281215, 81], [6291200, 82], [6291295, 83],
            [6291335, 84], [6291375, 85], [6291415, 86], [6291455, 87],
            [6447714, 241], [7105644, 242], [7763574, 243], [8388608, 1],
            [8388736, 5], [8421376, 3], [8421504, 8], [8421504, 244],
            [8847360, 88], [8847455, 89], [8847495, 90], [8847535, 91],
            [8847575, 92], [8847615, 93], [8871680, 94], [8871775, 95],
            [8871815, 96], [8871855, 97], [8871895, 98], [8871935, 99],
            [8881920, 100], [8882015, 101], [8882055, 102], [8882095, 103],
            [8882135, 104], [8882175, 105], [8892160, 106], [8892255, 107],
            [8892295, 108], [8892335, 109], [8892375, 110], [8892415, 111],
            [8902400, 112], [8902495, 113], [8902535, 114], [8902575, 115],
            [8902615, 116], [8902655, 117], [8912640, 118], [8912735, 119],
            [8912775, 120], [8912815, 121], [8912855, 122], [8912895, 123],
            [9079434, 245], [9737364, 246], [10395294, 247], [11053224, 248],
            [11468800, 124], [11468895, 125], [11468935, 126], [11468975, 127],
            [11469015, 128], [11469055, 129], [11493120, 130], [11493215, 131],
            [11493255, 132], [11493295, 133], [11493335, 134], [11493375, 135],
            [11503360, 136], [11503455, 137], [11503495, 138], [11503535, 139],
            [11503575, 140], [11503615, 141], [11513600, 142], [11513695, 143],
            [11513735, 144], [11513775, 145], [11513815, 146], [11513855, 147],
            [11523840, 148], [11523935, 149], [11523975, 150], [11524015, 151],
            [11524055, 152], [11524095, 153], [11534080, 154], [11534175, 155],
            [11534215, 156], [11534255, 157], [11534295, 158], [11534335, 159],
            [11711154, 249], [12369084, 250], [12632256, 7], [13027014, 251],
            [13684944, 252], [14090240, 160], [14090335, 161], [14090375, 162],
            [14090415, 163], [14090455, 164], [14090495, 165], [14114560, 166],
            [14114655, 167], [14114695, 168], [14114735, 169], [14114775, 170],
            [14114815, 171], [14124800, 172], [14124895, 173], [14124935, 174],
            [14124975, 175], [14125015, 176], [14125055, 177], [14135040, 178],
            [14135135, 179], [14135175, 180], [14135215, 181], [14135255, 182],
            [14135295, 183], [14145280, 184], [14145375, 185], [14145415, 186],
            [14145455, 187], [14145495, 188], [14145535, 189], [14155520, 190],
            [14155615, 191], [14155655, 192], [14155695, 193], [14155735, 194],
            [14155775, 195], [14342874, 253], [15000804, 254], [15658734, 255],
            [16711680, 9], [16711680, 196], [16711775, 197], [16711815, 198],
            [16711855, 199], [16711895, 200], [16711935, 13], [16711935, 201],
            [16736000, 202], [16736095, 203], [16736135, 204], [16736175, 205],
            [16736215, 206], [16736255, 207], [16746240, 208], [16746335, 209],
            [16746375, 210], [16746415, 211], [16746455, 212], [16746495, 213],
            [16756480, 214], [16756575, 215], [16756615, 216], [16756655, 217],
            [16756695, 218], [16756735, 219], [16766720, 220], [16766815, 221],
            [16766855, 222], [16766895, 223], [16766935, 224], [16766975, 225],
            [16776960, 11], [16776960, 226], [16777055, 227], [16777095, 228],
            [16777135, 229], [16777175, 230], [16777215, 15], [16777215, 231],
        ];

        $result = 0;

        $prev = -1;
        foreach ($table as list($truecolor, $id)) {

            if ($color >= $truecolor) {
                if ($id > 231 && ! $grayscale) {
                    continue;
                }
                if ($prev === $truecolor) {
                    continue;
                }
                $result = $id;
                $prev = $truecolor;
                continue;
            }
            break;
        }



        return $result;
    }

    protected static function degradeToAnsi(int $red, int $green, int $blue): string
    {

        if (0 === round(self::getSaturation($red, $green, $blue) / 50)) {
            return 0;
        }

        return (string) ((round($blue / 255) << 2) | (round($green / 255) << 1) | round($red / 255));
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

        return (int) ($diff * 100 / $value);
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
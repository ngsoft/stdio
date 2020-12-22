<?php

namespace NGSOFT\STDIO;

use NGSOFT\STDIO\Utils\ArrayObject;

class Styles extends ArrayObject {

    public static $colors = [
        'BLACK' => 30,
        'RED' => 31,
        'GREEN' => 32,
        'YELLOW' => 33,
        'BLUE' => 34,
        'MAGENTA' => 35,
        'CYAN' => 36,
        'GRAY' => 37,
        'DEFAULT' => 39
    ];
    public static $styles = [
        'RESET' => 0,
        'BRIGHT' => 1,
        'DIM' => 2,
        'ITALIC' => 3,
        'UNDERSCORE' => 4,
        'BLINK' => 5,
        'REVERSE' => 7,
        'HIDDEN' => 8,
    ];
    public static $replacements = [
        "\t" => "    ",
        "\s" => " ",
    ];

    const BG_COLOR_MODIFIER = 10;
    const TRUE_COLOR_MODIFIER = 60;
    const ESCAPE = "\033[";
    const SUFFIX_STYLE = "m";
    const CLEAR_LINE = self::ESCAPE . "2K";
    const CLEAR_START_LINE = self::ESCAPE . "1K";
    const CLEAR_END_LINE = self::ESCAPE . "K";
    const LINE_BREAK = "\n";
    const RETURN = "\r";

    public function __construct() {
        //parent::__construct($array);
    }

}

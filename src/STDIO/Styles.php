<?php

namespace NGSOFT\STDIO;

use NGSOFT\STDIO\Utils\ArrayObject;

class Styles extends ArrayObject {

    // Colors
    const COLOR_DEFAULT = 39;
    const COLOR_BLACK = 30;
    const COLOR_RED = 31;
    const COLOR_GREEN = 32;
    const COLOR_YELLOW = 33;
    const COLOR_BLUE = 34;
    const COLOR_MAGENTA = 35;
    const COLOR_CYAN = 36;
    const COLOR_GRAY = 37;

    public static $colors = [
        //color => [set, unset]
        'BLACK' => [30, 39],
        'RED' => [31, 39],
        'GREEN' => [32, 39],
        'YELLOW' => [33, 39],
        'BLUE' => [34, 39],
        'MAGENTA' => [35, 39],
        'CYAN' => [36, 39],
        'WHITE' => [37, 39],
        'GRAY' => [90, 39],
        'BRIGHTRED' => [91, 39],
        'BRIGHTGREEN' => [92, 39],
        'BRIGHTYELLOW' => [93, 39],
        'BRIGHTBLUE' => [94, 39],
        'BRIGHTMAGENTA' => [95, 39],
        'BRIGHTCYAN' => [96, 39],
        'BRIGHTWHITE' => [97, 39],
    ];
    public static $bg = [
        //color => [set, unset]
        'BLACK' => [40, 49],
        'RED' => [41, 49],
        'GREEN' => [42, 49],
        'YELLOW' => [43, 49],
        'BLUE' => [44, 49],
        'MAGENTA' => [45, 49],
        'CYAN' => [46, 49],
        'WHITE' => [47, 49],
        'GRAY' => [100, 49],
        'BRIGHTRED' => [101, 49],
        'BRIGHTGREEN' => [102, 49],
        'BRIGHTYELLOW' => [103, 49],
        'BRIGHTBLUE' => [104, 49],
        'BRIGHTMAGENTA' => [105, 49],
        'BRIGHTCYAN' => [106, 49],
        'BRIGHTWHITE' => [107, 49],
    ];
    public static $styles = [
        //style => [set, unset]
        'RESET' => [0, 0],
        'BOLD' => [1, 22],
        'DIM' => [2, 22],
        'ITALIC' => [3, 23],
        'UNDERLINE' => [4, 24],
        'INVERSE' => [7, 27],
        'HIDDEN' => [8, 28],
        'STRIKETROUGH' => [9, 29]
    ];
    public static $replacements = [
        "\t" => "    ",
        "\s" => " ",
    ];

    const BG_COLOR_MODIFIER = 10;
    const TRUE_COLOR_MODIFIER = 60;
    const ESCAPE = "\033[";
    const STYLE_SUFFIX = "m";
    const CLEAR_LINE = self::ESCAPE . "2K";
    const CLEAR_START_LINE = self::ESCAPE . "1K";
    const CLEAR_END_LINE = self::ESCAPE . "K";
    const LINE_BREAK = "\n";
    const RETURN = "\r";

    public function __construct() {
        //parent::__construct($array);
    }

}

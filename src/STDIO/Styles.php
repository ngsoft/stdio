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
    // Styles
    const STYLE_RESET = 0;
    const STYLE_BRIGHT = 1;
    const STYLE_DIM = 2;
    const STYLE_ITALIC = 3;
    const STYLE_UNDERSCORE = 4;
    const STYLE_BLINK = 5;
    const STYLE_REVERSE = 7;
    const STYLE_HIDDEN = 8;

    public static $colors = [
        'BLACK' => self::COLOR_BLACK,
        'RED' => self::COLOR_RED,
        'GREEN' => self::COLOR_GREEN,
        'YELLOW' => self::COLOR_YELLOW,
        'BLUE' => self::COLOR_BLUE,
        'MAGENTA' => self::COLOR_MAGENTA,
        'CYAN' => self::COLOR_CYAN,
        'GRAY' => self::COLOR_GRAY,
        'DEFAULT' => self::COLOR_DEFAULT
    ];
    public static $styles = [
        'RESET' => self::STYLE_RESET,
        'BRIGHT' => self::STYLE_BRIGHT,
        'DIM' => self::STYLE_DIM,
        'ITALIC' => self::STYLE_ITALIC,
        'UNDERSCORE' => self::STYLE_UNDERSCORE,
        'BLINK' => self::STYLE_BLINK,
        'REVERSE' => self::STYLE_REVERSE,
        'HIDDEN' => self::STYLE_HIDDEN,
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

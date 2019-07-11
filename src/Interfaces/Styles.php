<?php

namespace NGSOFT\Tools\Interfaces;

/**
 * @link http://www.termsys.demon.co.uk/vtansi.htm
 */
class Styles {

    ////////////////////////////   COLORS   ////////////////////////////

    const COLOR_DEFAULT = 39;
    const COLOR_BLACK = 30;
    const COLOR_RED = 31;
    const COLOR_GREEN = 32;
    const COLOR_YELLOW = 33;
    const COLOR_BLUE = 34;
    const COLOR_MAGENTA = 35;
    const COLOR_CYAN = 36;
    const COLOR_GRAY = 37;
    ////////////////////////////   COLORS+   ////////////////////////////

    const COLOR_MODIFIER_LIGHT = 60;
    const COLOR_MODIFIER_BACKGROUND = 10;
    ////////////////////////////   STYLES   ////////////////////////////

    const STYLE_RESET = 0;
    const STYLE_BRIGHT = 1;
    const STYLE_DIM = 2;
    const STYLE_ITALIC = 3;
    const STYLE_UNDERSCORE = 4;
    const STYLE_BLINK = 5;
    const STYLE_REVERSE = 7;
    const STYLE_HIDDEN = 8;
////////////////////////////   PREFIX/SUFFIX   ////////////////////////////

    const ESCAPE = "\033[";
    const STYLE_SUFFIX = "m";
    const ERASE_CURRENT_LINE = "2K";







    /*
     * Foreground and Background colors
     */
    const black = 'black';
    const dark_gray = 'dark_gray';
    const blue = 'blue';
    const light_blue = 'light_blue';
    const green = 'green';
    const light_green = 'light_green';
    const cyan = 'cyan';
    const light_cyan = 'light_cyan';
    const red = 'red';
    const light_red = 'light_red';
    const purple = 'purple';
    const light_purple = 'light_purple';
    const brown = 'brown';
    const yellow = 'yellow';
    const light_gray = 'light_gray';
    const white = 'white';
    const magenta = 'magenta';
    const light_yellow = 'light_yellow';
    const light_magenta = 'light_magenta';

    /*
     * Styles
     */
    const s_reset = 'reset';
    const s_bold = 'bold';
    const s_dark = 'dark';
    const s_italic = 'italic';
    const s_underline = 'underline';
    const s_blink = 'blink';
    const s_reverse = 'reverse';
    const s_concealed = 'concealed';

    protected $background = [
        'default' => 49,
        'black' => 40,
        'red' => 41,
        'green' => 42,
        'yellow' => 43,
        'blue' => 44,
        'magenta' => 45,
        'cyan' => 46,
        'light_gray' => 47,
        'dark_gray' => 100,
        'light_red' => 101,
        'light_green' => 102,
        'light_yellow' => 103,
        'light_blue' => 104,
        'light_magenta' => 105,
        'light_cyan' => 106,
        'white' => 107
    ];
    protected $colors = [
        'default' => 39,
        'black' => 30,
        'red' => 31,
        'green' => 32,
        'yellow' => 33,
        'blue' => 34,
        'magenta' => 35,
        'cyan' => 36,
        'light_gray' => 37,
        'dark_gray' => 90,
        'light_red' => 91,
        'light_green' => 92,
        'light_yellow' => 93,
        'light_blue' => 94,
        'light_magenta' => 95,
        'light_cyan' => 96,
        'white' => 97
    ];
    protected $style = [
        'reset' => 0,
        'bold' => 1,
        'dark' => 2,
        'italic' => 3,
        'underline' => 4,
        'blink' => 5,
        'reverse' => 7,
        'concealed' => 8
    ];

}

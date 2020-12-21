<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Interfaces;

/**
 * @link http://www.termsys.demon.co.uk/vtansi.htm
 */
interface Styles {

    ////////////////////////////   COLORS   ////////////////////////////
    //const COLOR_DEFAULT = 39;
    const COLOR_BLACK = 30;
    const COLOR_RED = 31;
    const COLOR_GREEN = 32;
    const COLOR_YELLOW = 33;
    const COLOR_BLUE = 34;
    const COLOR_MAGENTA = 35;
    const COLOR_CYAN = 36;
    const COLOR_GRAY = 37;
    ////////////////////////////   COLORS+   ////////////////////////////

    const COLOR_MODIFIER_TRUECOLOR = 60;
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

    const PREFIX_ESCAPE = "\033[";
    const SUFFIX_STYLE = "m";
    const ERASE_CURRENT_LINE = "2K";

}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Interfaces;

interface Ansi {

    const ESCAPE = "\033[";
    const STYLE_SUFFIX = "m";
    const CLEAR_END_LINE = self::ESCAPE . 'K';
    const CLEAR_START_LINE = self::ESCAPE . '1K';
    const CLEAR_LINE = self::ESCAPE . '2K';
    const CLEAR_DOWN = self::ESCAPE . 'J';
    const CLEAR_UP = self::ESCAPE . '1J';
    const CLEAR_SCREEN = self::ESCAPE . '2J';
    const SCROLL_UP = self::ESCAPE . 'S';
    const SCROLL_DOWN = self::ESCAPE . 'T';
    const CURSOR_SUFFIX_TO = 'H';
    const CURSOR_UP = self::ESCAPE . 'A';
    const CURSOR_DOWN = self::ESCAPE . 'B';
    const CURSOR_RIGHT = self::ESCAPE . 'C';
    const CURSOR_LEFT = self::ESCAPE . 'D';
    const CURSOR_NEXT_LINE = self::ESCAPE . 'E';
    const CURSOR_PREV_LINE = self::ESCAPE . 'F';
    const CURSOR_SAVE_POS = self::ESCAPE . 's';
    const CURSOR_LOAD_POS = self::ESCAPE . 'u';

}

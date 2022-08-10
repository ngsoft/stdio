<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Enums;

interface Ansi
{

    public const ESCAPE = "\x1b[";
    public const STYLE_SUFFIX = "m";
    public const CLEAR_END_LINE = self::ESCAPE . 'K';
    public const CLEAR_START_LINE = self::ESCAPE . '1K';
    public const CLEAR_LINE = self::ESCAPE . '2K';
    public const CLEAR_DOWN = self::ESCAPE . '0J';
    public const CLEAR_UP = self::ESCAPE . '1J';
    public const CLEAR_SCREEN = self::ESCAPE . '2J';
    public const SCROLL_UP = self::ESCAPE . 'S';
    public const SCROLL_DOWN = self::ESCAPE . 'T';
    public const CURSOR_SUFFIX_TO = 'H';
    public const CURSOR_UP = self::ESCAPE . '%uA';
    public const CURSOR_DOWN = self::ESCAPE . '%uB';
    public const CURSOR_RIGHT = self::ESCAPE . '%uC';
    public const CURSOR_LEFT = self::ESCAPE . '%uD';
    public const CURSOR_COL = self::ESCAPE . '%uG';
    public const CURSOR_POS = self::ESCAPE . '%u;%uH';
    public const CURSOR_NEXT_LINE = self::ESCAPE . 'E';
    public const CURSOR_PREV_LINE = self::ESCAPE . 'F';
    public const CURSOR_SAVE_POS = "\x1b7";
    public const CURSOR_LOAD_POS = "\x1b8";

}

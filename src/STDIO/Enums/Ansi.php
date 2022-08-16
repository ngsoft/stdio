<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Enums;

/**
 * @link https://gist.github.com/fnky/458719343aabd01cfb17a3a4f7296797
 */
interface Ansi
{

    /**
     * Sequences
     */
    public const ESC = '\x1b';
    public const CSI = '\x1b[';
    public const OSC = '\x1b]';

    /**
     * Styles
     */
    public const RESET = self::CSI . '0m';
    public const STYLE = self::CSI . '%sm';

    /**
     * Cursor control
     */
    public const CURSOR_UP = self::CSI . '%uA';
    public const CURSOR_DOWN = self::CSI . '%uB';
    public const CURSOR_RIGHT = self::CSI . '%uC';
    public const CURSOR_LEFT = self::CSI . '%uD';
    public const CURSOR_NEXT_LINE = self::CSI . '%uE';
    public const CURSOR_PREV_LINE = self::CSI . '%uF';
    public const CURSOR_COL = self::CSI . '%uG';
    public const CURSOR_POS = self::CSI . '%u;%uH';

    /**
     * Erase Screen
     */
    public const CLEAR_DOWN = self::CSI . '0J';
    public const CLEAR_UP = self::CSI . '1J';
    public const CLEAR_SCREEN = self::CSI . '2J';

    /**
     * Erase Line
     */
    public const CLEAR_END_LINE = self::CSI . '0K';
    public const CLEAR_START_LINE = self::CSI . '1K';
    public const CLEAR_LINE = self::CSI . '2K';

    /**
     * Scroll
     */
    public const SCROLL_UP = self::CSI . '%uS';
    public const SCROLL_DOWN = self::CSI . '%uT';

    /**
     * Save
     */
    public const CURSOR_SAVE_POS = '\x1b7';
    public const CURSOR_LOAD_POS = '\x1b8';

    /**
     * Visible
     */
    public const CURSOR_HIDE = self::ESC . '?25l';
    public const CURSOR_SHOW = self::ESC . '?25h';

}

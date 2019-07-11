<?php

namespace NGSOFT\Tools;

use NGSOFT\Tools\{
    Interfaces\Styles, IO\Outputs\Output
};
use ReflectionClass;

class STDIO {

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

    /** @var array<string,int> */
    private static $keywords = [
    ];

    /** @var Output */
    private $stdout;

    /** @var Output */
    private $stderr;

    /** @var Formatter */
    private $formatter;

    public function __construct() {
        if (empty(self::$keywords)) $this->parseKeyWords();
    }

    private function parseKeyWords() {
        foreach ((new ReflectionClass($this))->getReflectionConstants() as $const) {

            if (preg_match('/^(?:(COLOR|STYLE)\_)([A-Z]+)$/', $const->name, $matches)) {
                list(, $mode, $keyword) = $matches;
                self::$keywords[strtolower($keyword)] = $const->getValue();

                if ($mode === 'COLOR') {
                    self::$keywords["bg-" . strtolower($keyword)] = self::COLOR_MODIFIER_BACKGROUND + $const->getValue();
                    self::$keywords["tc-" . strtolower($keyword)] = self::COLOR_MODIFIER_TRUECOLOR + $const->getValue();
                    self::$keywords["bg-tc-" . strtolower($keyword)] = (
                            self::COLOR_MODIFIER_BACKGROUND + self::COLOR_MODIFIER_TRUECOLOR + $const->getValue()
                            );
                }
            }

            //$keyword = preg_replace('/^(?:(COLOR|STYLE)\_)([A-Z]+)$/', '$2', $const->name);
            //if ($keyword !== $const->name) self::$keywords[strtolower($keyword)] = $const->getValue();
        }
    }

    public static function create(...$args) {
        return new static(...$args);
    }

}

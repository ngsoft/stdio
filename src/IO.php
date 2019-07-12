<?php

namespace NGSOFT\Tools;

use NGSOFT\Tools\{
    Interfaces\InputInterface, Interfaces\OutputInterface, IO\Inputs\STDIN, IO\Outputs\BufferOutput, IO\Outputs\STDERR,
    IO\Outputs\STDOUT, IO\Terminal
};
use RuntimeException;

/**
 * Basic CLI Formatter
 *  Resources used for this project
 * @link http://www.termsys.demon.co.uk/vtansi.htm
 * @link https://jonasjacek.github.io/colors/
 * @link https://stackoverflow.com/questions/4842424/list-of-ansi-color-escape-sequences
 */
class IO {

    /**
     * STDIO Version
     */
    const VERSION = '1.0.0';

    /**
     * Basic Terminal Colors
     * @link URL description
     */
    const COLOR_BLACK = 30;
    const COLOR_RED = 31;
    const COLOR_GREEN = 32;
    const COLOR_YELLOW = 33;
    const COLOR_BLUE = 34;
    const COLOR_MAGENTA = 35;
    const COLOR_CYAN = 36;
    const COLOR_GRAY = 37;
    const COLOR_DEFAULT = 39;

    /**
     * Basic Terminal Styles
     */
    const STYLE_BRIGHT = 1;
    const STYLE_UNDERSCORE = 4;
    const STYLE_BLINK = 5;
    const STYLE_REVERSE = 7;
    const STYLE_CONCEAL = 8;

    //const STYLE_RESET = 0;

    /** @var InputInterface */
    private static $stdin;

    /** @var OutputInterface */
    private static $stdout;

    /** @var OutputInterface */
    private static $stderr;

    /** @var Terminal */
    private static $term;

    /** @var BufferOutput */
    private $buffer;

    private static function initialize() {
        if (!isset(self::$stdin)) {
            if (php_sapi_name() !== "cli") throw new RuntimeException(__CLASS__ . " can only be run under CLI Environnement");
            self::$stdout = new STDOUT();
            self::$stderr = new STDERR();
            self::$stdin = new STDIN();
            self::$term = new Terminal;
        }
    }

    /**
     * Creates a new instance
     * @return static
     */
    public static function create() {
        return new static();
    }

    public function __construct() {
        self::initialize();
        $this->buffer = new BufferOutput();
    }

}

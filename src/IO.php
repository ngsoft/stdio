<?php

namespace NGSOFT\Tools;

use NGSOFT\Tools\{
    Interfaces\FormatterInterface, Interfaces\InputInterface, Interfaces\OutputInterface, Interfaces\StyleSheetInterface,
    IO\Formatters\PlainTextFormatter, IO\Inputs\STDIN, IO\Outputs\BufferOutput, IO\Outputs\STDERR, IO\Outputs\STDOUT,
    IO\Styles\StyleSheet, IO\Terminal
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

    /** @var STDIN */
    private static $stdin;

    /** @var STDOUT */
    private static $stdout;

    /** @var STDERR */
    private static $stderr;

    /** @var Terminal */
    private static $term;

    /** @var FormatterInterface */
    private $formatter;

    /** @var StyleSheetInterface */
    private $stylesheet;

    /** @var BufferOutput */
    private $buffer;

    ////////////////////////////   CONFIGURATION   ////////////////////////////


    private function initialize() {
        if (!isset(self::$stdin)) {
            if (php_sapi_name() !== "cli") throw new RuntimeException(__CLASS__ . " can only be run under CLI Environnement");
            self::$stdout = new STDOUT();
            self::$stderr = new STDERR();
            self::$stdin = new STDIN();
            self::$term = new Terminal();
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
        $this->initialize();
        $this->buffer = new BufferOutput();
        $this->stylesheet = new StyleSheet();
        $this->formatter = new PlainTextFormatter();
    }

    /**
     * Access the stream directly
     * @return InputInterface
     */
    public function getSTDIN(): STDIN {
        return self::$stdin;
    }

    /**
     * Access the stream directly
     * @return OutputInterface
     */
    public function getSTDOUT(): STDOUT {
        return self::$stdout;
    }

    /**
     * Access the stream directly
     * @return OutputInterface
     */
    public function getSTDERR(): STDERR {
        return self::$stderr;
    }

    /**
     * Get Formatter
     * @return FormatterInterface
     */
    public function getFormatter(): FormatterInterface {
        return $this->formatter;
    }

    /**
     * Get Stylesheet
     * @return StyleSheetInterface
     */
    public function getStylesheet(): StyleSheetInterface {
        return $this->stylesheet;
    }

    /**
     * Set the Formatter
     * @param FormatterInterface $formatter
     * @return static
     */
    public function setFormatter(FormatterInterface $formatter) {
        $this->formatter = $formatter;
        return $this;
    }

    /**
     * Get the Stylesheet
     * @param StyleSheetInterface $stylesheet
     * @return static
     */
    public function setStylesheet(StyleSheetInterface $stylesheet) {
        $this->stylesheet = $stylesheet;
        return $this;
    }

    ////////////////////////////   Read and Print   ////////////////////////////

    public function prompt(string $question = null, string $classList = "question"): string {
        if ($question !== null) {
            if (empty($classList)) $format = "$question ";
            else $format = sprintf("<span class=\"%s\">%s</span> ", $classList, $question);

            $this->getSTDOUT()->write($format, false, $this->formatter);
        }
        return $this->getSTDIN()->readln();
    }

    public function confirm(
            string $question = null,
            bool $default = false,
            array $yes = ["y"],
            array $no = ["n"],
            string $classList = "question"
    ): bool {
        return true;
    }

}

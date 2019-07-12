<?php

declare(strict_types=1);

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
    private $stdin;

    /** @var STDOUT */
    private $stdout;

    /** @var STDERR */
    private $stderr;

    /** @var Terminal */
    private $term;

    /** @var FormatterInterface */
    private $formatter;

    /** @var StyleSheetInterface */
    private $stylesheet;

    /** @var BufferOutput */
    private $buffer;

    ////////////////////////////   CONFIGURATION   ////////////////////////////


    private function initialize() {

        if (php_sapi_name() !== "cli") throw new RuntimeException(__CLASS__ . " can only be run under CLI Environnement");
        $this->stdout = new STDOUT();
        $this->stderr = new STDERR();
        $this->stdin = new STDIN();
        $this->term = new Terminal();
        $this->buffer = new BufferOutput();
        $this->stylesheet = new StyleSheet();
        //defines formatter
        $this->formatter = new PlainTextFormatter();

        foreach ([$this->stderr, $this->stdout] as $out) {
            $out->setFormatter($this->formatter);
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
    }

    /**
     * Access the stream directly
     * @return InputInterface
     */
    public function getSTDIN(): STDIN {
        return $this->stdin;
    }

    /**
     * Access the stream directly
     * @return OutputInterface
     */
    public function getSTDOUT(): STDOUT {
        return $this->stdout;
    }

    /**
     * Access the stream directly
     * @return OutputInterface
     */
    public function getSTDERR(): STDERR {
        return $this->stderr;
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

    /**
     * Prompt for a value
     * @param string $question
     * @param string $classList
     * @return string
     */
    public function prompt(string $question = null, string $classList = "question"): string {
        if ($question !== null) {
            $format = "$question ";
            if (!empty($classList)) $format = sprintf("<span class=\"%s\">%s</span> ", $classList, $format);

            $this->getSTDOUT()->write($format, false, $this->formatter);
        }
        return $this->getSTDIN()->readln();
    }

    /**
     * Prompt for a confirmation
     * @param string $question
     * @param bool $default
     * @param array $yes
     * @param array $no
     * @param string $classList
     * @return bool
     */
    public function confirm(
            string $question = "",
            bool $default = false,
            array $yes = [],
            array $no = [],
            string $classList = "question"
    ): bool {

        if (empty($yes)) $yes = ["yes", "y"];
        if (empty($no)) $no = ["no", "n"];
        $norm_yes = array_map("strtolower", $yes);
        $norm_no = array_map("strtolower", $no);
        $prompt = sprintf(
                '%s [%s|%s] [%s] ',
                $question, implode("/", $yes), implode("/", $no),
                $default === true ? $yes[0] : $no[0]
        );

        if (!empty($classList)) $prompt = sprintf('<span class="%s">%s</span>', $classList, $prompt);

        $answer = null;
        while (!is_bool($answer)) {
            $this->getSTDOUT()->write($prompt);
            $line = strtolower($this->getSTDIN()->readln());
            $answer = empty($line) ? $default :
                    (in_array($line, $norm_yes) ? true :
                    (in_array($line, $norm_no) ? false : null));
        }

        return $answer;
    }

}

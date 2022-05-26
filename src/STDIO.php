<?php

declare(strict_types=1);

namespace NGSOFT;

use NGSOFT\STDIO\{
    Cursor, Formatters\TagFormatter, Inputs\Input, Outputs\Buffer, Outputs\ErrorOutput, Outputs\Output, StyleSheet, Terminal
};

/**
 * STDIO Super Object
 * Gives access to all components
 *
 */
final class STDIO {

    public const VERSION = '3.0';

    private Output $output;
    private ErrorOutput $errorOutput;
    private Input $input;
    private Buffer $buffer;
    private Terminal $terminal;
    private Cursor $cursor;
    private StyleSheet $styles;
    private TagFormatter $formatter;

    /**
     * Get STDIO Instance
     *
     * @staticvar type $instance
     * @return static
     */
    public static function create(): static {
        static $instance;
        $instance = $instance ?? new static();
        return $instance;
    }

    /**
     * @param ?bool $forceColorSupport Overrides color support detection
     */
    public function __construct(bool $forceColorSupport = null) {
        $this->terminal = Terminal::create();
        $this->styles = new StyleSheet($forceColorSupport);
        $this->formatter = new TagFormatter($this->styles);
        $this->buffer = new Buffer();
        $this->output = new Output($this->formatter);
        $this->errorOutput = new ErrorOutput($this->formatter);
        $this->input = new Input();
        $this->cursor = new Cursor($this->output, $this->input);
    }

    public function getOutput(): Output {
        return $this->output;
    }

    public function getErrorOutput(): ErrorOutput {
        return $this->errorOutput;
    }

    public function getInput(): Input {
        return $this->input;
    }

    public function getBuffer(): Buffer {
        return $this->buffer;
    }

    public function getTerminal(): Terminal {
        return $this->terminal;
    }

    public function getCursor(): Cursor {
        return $this->cursor;
    }

    public function getStyles(): StyleSheet {
        return $this->styles;
    }

    public function getFormatter(): TagFormatter {
        return $this->formatter;
    }

}

<?php

declare(strict_types=1);

namespace NGSOFT;

use NGSOFT\STDIO\{
    Cursor, Inputs\Input, Outputs\Buffer, Outputs\ErrorOutput, Outputs\Output, StyleSheet, Terminal
};

final class STDIO {

    public const VERSION = '3.0';

    private Output $output;
    private ErrorOutput $errorOutput;
    private Input $input;
    private Buffer $buffer;
    private Terminal $terminal;
    private Cursor $cursor;
    private StyleSheet $styles;

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
        $this->output = new Output();
        $this->errorOutput = new ErrorOutput();
        $this->input = new Input();
        $this->buffer = new Buffer();
        $this->terminal = Terminal::create();
        $this->cursor = new Cursor($this->output, $this->input);
        $this->styles = new StyleSheet($forceColorSupport);
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

}

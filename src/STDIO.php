<?php

declare(strict_types=1);

namespace NGSOFT;

use NGSOFT\STDIO\{
    Cursor, Inputs\Input, Outputs\Buffer, Outputs\ErrorOutput, Outputs\Output, Terminal
};

final class STDIO {

    public const VERSION = '3.0';

    /** @var Output */
    private $output;

    /** @var ErrorOutput */
    private $errorOutput;

    /** @var Input */
    private $input;

    /** @var Buffer */
    private $buffer;

    /** @var Terminal */
    private $terminal;

    /** @var Cursor */
    private $cursor;

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

    public function __construct() {
        $this->output = new Output();
        $this->errorOutput = new ErrorOutput();
        $this->input = new Input();
        $this->buffer = new Buffer();
        $this->terminal = Terminal::create();
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

}

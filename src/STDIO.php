<?php

declare(strict_types=1);

namespace NGSOFT;

use NGSOFT\STDIO\{
    Cursor, Formatters\TagFormatter, Inputs\Input, Outputs\Buffer, Outputs\ErrorOutput, Outputs\Output, StyleSheet, Terminal
};
use Stringable;

/**
 * STDIO Super Object
 * Gives access to all components
 *
 */
final class STDIO
{

    public const VERSION = '3.0';

    private Output $output;
    private ErrorOutput $errorOutput;
    private Input $input;
    private Buffer $buffer;
    private Cursor $cursor;
    private StyleSheet $styles;
    private TagFormatter $formatter;

    /**
     * Get STDIO Instance
     *
     * @staticvar type $instance
     * @return static
     */
    public static function create(): static
    {
        static $instance;
        $instance = $instance ?? new static();
        return $instance;
    }

    /**
     * @param ?bool $forceColorSupport Overrides color support detection
     */
    public function __construct(bool $forceColorSupport = null)
    {
        $this->styles = new StyleSheet($forceColorSupport);
        $this->formatter = new TagFormatter($this->styles);

        $this->buffer = new Buffer();
        $this->output = new Output($this->formatter);
        $this->errorOutput = new ErrorOutput($this->formatter);
        $this->input = new Input();
        $this->cursor = new Cursor($this->output, $this->input);
    }

    public function getOutput(): Output
    {
        return $this->output;
    }

    public function getErrorOutput(): ErrorOutput
    {
        return $this->errorOutput;
    }

    public function getInput(): Input
    {
        return $this->input;
    }

    public function getBuffer(): Buffer
    {
        return $this->buffer;
    }

    public function getCursor(): Cursor
    {
        return $this->cursor;
    }

    public function getStyles(): StyleSheet
    {
        return $this->styles;
    }

    public function getFormatter(): TagFormatter
    {
        return $this->formatter;
    }

    ////////////////////////////   Helpers   ////////////////////////////

    /**
     * Write message into the buffer
     *
     * @param string|Stringable|array $messages
     * @return static
     */
    public function write(string|Stringable|array $messages): static
    {
        $this->buffer->write($messages);
        return $this;
    }

    /**
     *  Write message into the buffer and creates a new line
     * @param string|Stringable|array $messages
     * @return static
     */
    public function writeln(string|Stringable|array $messages): static
    {
        $this->buffer->writeln($messages);
        return $this;
    }

    /**
     * Prints message or flush buffer to the output
     *
     * @param string|Stringable|array|null $messages flush the buffer if set to null
     * @return static
     */
    public function out(string|Stringable|array $messages = null): static
    {

        if ( ! is_null($messages)) {
            $this->buffer->clear();
            $this->output->write($messages);
        } else $this->buffer->flush($this->output);
        return $this;
    }

    /**
     * Prints message or flush buffer to the error output
     *
     * @param string|Stringable|string[]|Stringable[]|null $messages flush the buffer if set to null
     * @return static
     */
    public function err(string|Stringable|array $messages = null): static
    {
        if ( ! is_null($messages)) {
            $this->buffer->clear();
            $this->errorOutput->write($messages);
        } else $this->buffer->flush($this->errorOutput);
        return $this;
    }

    /**
     * Read lines from the input
     *
     * @param int $lines
     * @param bool $allowEmptyLines
     * @return string[]|string
     */
    public function read(int $lines = 1, bool $allowEmptyLines = true): array|string
    {
        $result = $this->input->read($lines, $allowEmptyLines);
        return $lines === 1 ? $result[0] : $result;
    }

}

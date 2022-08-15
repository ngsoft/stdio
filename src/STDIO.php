<?php

declare(strict_types=1);

namespace NGSOFT;

use NGSOFT\STDIO\{
    Cursor, Formatters\Formatter, Formatters\TagFormatter, Formatters\Tags\Rect as TagRect, Helpers\Rect, Inputs\Input, Outputs\Buffer, Outputs\ErrorOutput, Outputs\Output,
    Styles\Style, Styles\Styles
};
use Stringable;

/**
 * STDIO Super Object
 * Gives access to all components
 */
class STDIO
{

    public const VERSION = '3.0';

    protected static self $_instance;
    protected Output $output;
    protected ErrorOutput $errorOutput;
    protected Input $input;
    protected Buffer $buffer;
    protected Cursor $cursor;
    protected Styles $styles;
    protected Formatter $formatter;

    /**
     * Get STDIO Instance
     */
    final public static function create(bool $forceColorSupport = null): static
    {
        static $cache = [];
        return $cache[json_encode($forceColorSupport)] ??= new static($forceColorSupport);
    }

    /**
     * Get last used STDIO Instance
     */
    final public static function getCurrentInstance(): static
    {
        return static::$_instance ??= static::create();
    }

    final public function __construct(bool $forceColorSupport = null)
    {
        static::$_instance = $this;

        $this->buffer = new Buffer();
        $this->input = new Input();
        $this->styles = $styles = new Styles($forceColorSupport);
        $this->formatter = $formatter = new TagFormatter($styles);
        $this->output = $output = new Output($formatter);
        $this->errorOutput = new ErrorOutput($formatter);
        $this->cursor = new Cursor($output);
    }

    public function getOutput(): Output
    {
        try {
            return $this->output;
        } finally {
            static::$_instance = $this;
        }
    }

    public function getErrorOutput(): ErrorOutput
    {
        try {
            return $this->errorOutput;
        } finally {
            static::$_instance = $this;
        }
    }

    public function getInput(): Input
    {
        try {
            return $this->input;
        } finally {
            static::$_instance = $this;
        }
    }

    public function getBuffer(): Buffer
    {
        try {
            return $this->buffer;
        } finally {
            static::$_instance = $this;
        }
    }

    public function getCursor(): Cursor
    {
        try {
            return $this->cursor;
        } finally {
            static::$_instance = $this;
        }
    }

    public function getStyles(): Styles
    {
        try {
            return $this->styles;
        } finally {
            static::$_instance = $this;
        }
    }

    public function getFormatter(): Formatter
    {
        try {
            return $this->formatter;
        } finally {
            static::$_instance = $this;
        }
    }

    ////////////////////////////   Helpers   ////////////////////////////

    /**
     * Write message into the buffer
     *
     * @param string|Stringable|string[]|Stringable[] $messages
     * @return static
     */
    public function write(string|Stringable|array $messages): static
    {
        $this->getBuffer()->write($messages);
        return $this;
    }

    /**
     *  Write message into the buffer and creates a new line
     * @param string|Stringable|string[]|Stringable[] $messages
     * @return static
     */
    public function writeln(string|Stringable|array $messages): static
    {
        $this->getBuffer()->writeln($messages);
        return $this;
    }

    /**
     * Renders to the selected output
     */
    public function render(Output $output, string|Stringable|array $messages = null): static
    {

        try {
            if (is_null($messages)) {
                $this->getBuffer()->render($output);
            } else { $output->write($messages); }
        } finally {
            $this->getBuffer()->clear();
        }

        return $this;
    }

    /**
     * Prints message or flush buffer to the output
     */
    public function out(string|Stringable|array $messages = null): static
    {
        return $this->render($this->getOutput(), $messages);
    }

    /**
     * Prints message or flush buffer to the error output
     */
    public function err(string|Stringable|array $messages = null): static
    {
        return $this->render($this->getErrorOutput(), $messages);
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
        $result = $this->getInput()->read($lines, $allowEmptyLines);
        return $lines === 1 ? $result[0] : $result;
    }

    /**
     * Adds a rectangle to the buffer
     *
     * @param string|\Stringable $message
     * @param Style|null|string $style use rect tag code without <> delimiters to set style
     * @param Rect &$rect
     * @return static
     */
    public function rect(string|\Stringable $message, Style|null|string $style = null, &$rect = null): static
    {
        $rect = (new TagRect($this->getStyles()))->createFromCode('rect;' . (is_string($style) ? $style : ''))->getRect();
        if ($style instanceof Style) {
            $rect->setStyle($style);
        }

        return $this->write($rect->write($message));
    }

}

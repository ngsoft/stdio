<?php

declare(strict_types=1);

namespace NGSOFT;

use NGSOFT\STDIO\{
    Cursor, Elements\Custom\Rect as RectElement, Formatters\Formatter, Formatters\TagFormatter, Helpers\Rect, Inputs\Input, Outputs\Buffer, Outputs\ErrorOutput,
    Outputs\Output, Styles\Style, Styles\StyleList
};
use Stringable;

/**
 * STDIO Super Object
 * Gives access to all components
 */
class STDIO
{

    public const VERSION = '3.0';

    protected static $_instances = [];
    protected static self $_instance;
    protected Output $output;
    protected ErrorOutput $errorOutput;
    protected Input $input;
    protected Buffer $buffer;
    protected Cursor $cursor;
    protected StyleList $styles;
    protected Formatter $formatter;

    /**
     * Get STDIO Instance
     */
    final public static function create(bool $forceColorSupport = null): static
    {
        return static::$_instances[json_encode($forceColorSupport)] ??= new static($forceColorSupport);
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


        $this->buffer = new Buffer();
        $this->input = new Input();
        $this->styles = $styles = new StyleList($forceColorSupport);
        $this->formatter = $formatter = new TagFormatter($styles);
        $this->output = new Output($formatter);
        $this->errorOutput = new ErrorOutput($formatter);
        static::$_instance = static::$_instances[json_encode($forceColorSupport)] = $this;
    }

    public function getOutput(): Output
    {
        static::$_instance = $this;
        return $this->output;
    }

    public function getErrorOutput(): ErrorOutput
    {
        static::$_instance = $this;
        return $this->errorOutput;
    }

    public function getInput(): Input
    {
        static::$_instance = $this;

        return $this->input;
    }

    public function getBuffer(): Buffer
    {

        static::$_instance = $this;

        return $this->buffer;
    }

    public function getStyles(): StyleList
    {
        static::$_instance = $this;
        return $this->styles;
    }

    public function getFormatter(): Formatter
    {
        static::$_instance = $this;
        return $this->formatter;
    }

    ////////////////////////////   Helpers   ////////////////////////////

    /**
     * Write message into the buffer using printf
     */
    public function printf(string $pattern, mixed ...$arguments): static
    {
        return $this->write(sprintf($pattern, ...$arguments));
    }

    /**
     * Writes message directly to the output
     */
    public function print(string|Stringable|array $messages, string $style = null): static
    {
        if (is_string($style)) {
            $style = $this->getStyles()->createFromStyleString($style);
            $messages = $style->format($messages);
        }
        return $this->render($this->getOutput(), $messages);
    }

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

        if ( ! is_string($style)) {
            $rect = Rect::create($this->getStyles());
            if ( ! is_null($style)) {
                $rect->setStyle($style);
            }
        } else { $rect = RectElement::create($style, $this->getStyles())->getRect(); }

        return $this->write($rect->write($message));
    }

}

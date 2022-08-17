<?php

declare(strict_types=1);

namespace NGSOFT;

use NGSOFT\STDIO\{
    Elements\Custom\Rect as RectElement, Formatters\Formatter, Formatters\TagFormatter, Helpers\Rectangle, Inputs\Input, Outputs\Buffer, Outputs\ErrorOutput, Outputs\Output,
    Styles\Style, Styles\StyleList
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
    protected ?Output $output = null;
    protected ?ErrorOutput $errorOutput = null;
    protected ?Input $input = null;
    protected ?Buffer $buffer = null;
    protected ?StyleList $styles = null;
    protected ?Formatter $formatter = null;

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
        static::$_instances[json_encode($forceColorSupport)] = static::$_instance = $this;
        $this->buffer = new Buffer();
        $this->input = new Input();
        $this->styles = new StyleList($forceColorSupport);
    }

    public function getOutput(): Output
    {
        return $this->output ??= new Output($this->getFormatter());
    }

    public function getErrorOutput(): ErrorOutput
    {
        return $this->errorOutput ??= new ErrorOutput($this->getFormatter());
    }

    public function getInput(): Input
    {
        return $this->input ??= new Input();
    }

    public function getBuffer(): Buffer
    {
        return $this->buffer;
    }

    public function getStyles(): StyleList
    {
        return $this->styles;
    }

    public function getFormatter(): Formatter
    {
        return $this->formatter ??= new TagFormatter($this->getStyles());
    }

    ////////////////////////////   Helpers   ////////////////////////////

    /**
     * Write message directly to the output using printf
     */
    public function printf(string $pattern, mixed ...$arguments): static
    {
        return $this->render($this->getOutput(), sprintf($pattern, ...$arguments));
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
     * Writes message directly to the output
     */
    public function println(string|Stringable|array $messages, string $style = null): static
    {
        if (is_string($style)) {
            $style = $this->getStyles()->createFromStyleString($style);
            $messages = $style->format($messages);
        }

        if ( ! is_array($messages)) {
            $messages = [$messages];
        }

        $messages[] = "\n";

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
     * @param Rectangle &$rect
     * @return static
     */
    public function rect(string|\Stringable $message, Style|null|string $style = null, &$rect = null): static
    {

        if ( ! is_string($style)) {
            $rect = Rectangle::create($this->getStyles());
            if ( ! is_null($style)) {
                $rect->setStyle($style);
            }
        } else { $rect = RectElement::create($style, $this->getStyles())->getRect(); }

        return $this->write($rect->write($message));
    }

}

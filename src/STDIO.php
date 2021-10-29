<?php

declare(strict_types=1);

namespace NGSOFT;

use BadMethodCallException,
    InvalidArgumentException;
use NGSOFT\STDIO\{
    Formatters\PlainText, Formatters\Tags, Inputs\StreamInput, Interfaces\Ansi, Interfaces\Buffer, Interfaces\Colors, Interfaces\Formats, Interfaces\Formatter,
    Interfaces\Input, Interfaces\Output, Outputs\OutputBuffer, Outputs\StreamOutput, Styles, Terminal, Utils\Progress, Utils\Rect
};

/**
 * @method STDIO black(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO red(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO green(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO yellow(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO blue(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO purple(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO cyan(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO white(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO gray(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO brightred(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO brightgreen(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO brightyellow(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO brightblue(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO brightpurple(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO brightcyan(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO brightwhite(?string $message) Adds the corresponding Style to the buffer
 *
 * @method STDIO info(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO comment(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO whisper(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO shout(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO error(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO notice(?string $message) Adds the corresponding Style to the buffer
 *
 * @method STDIO bold(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO dim(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO italic(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO underline(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO inverse(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO hidden(?string $message) Adds the corresponding Style to the buffer
 * @method STDIO striketrough(?string $message) Adds the corresponding Style to the buffer
 */
final class STDIO implements Ansi, Colors, Formats {

    const VERSION = '2.3';

    /** @var Terminal */
    private $terminal;

    /** @var bool */
    private $supportsColors;

    /** @var array<string,STDIO\Interfaces\Input> */
    private $inputs = [];

    /** @var array<string,STDIO\Interfaces\Output> */
    private $outputs = [];

    /** @var Buffer */
    private $buffer;

    /** @var Styles */
    private $styles;

    /** @var Formatter */
    private $formatter;

    ////////////////////////////   Initialisation   ////////////////////////////

    /**
     * Creates STDIO Instance
     * @return STDIO
     */
    public static function create(): STDIO {
        return new static();
    }

    public function __construct() {
        $this->terminal = new Terminal();
        $this->supportsColors = $this->terminal->hasColorSupport();
        $this->inputs['in'] = new StreamInput();
        $stdout = new StreamOutput();
        $this->outputs['out'] = $stdout;
        $this->outputs['err'] = $stdout->withStream(fopen('php://stderr', 'w'));
        $this->buffer = new OutputBuffer();
        $this->styles = Styles::create();

        if ($this->supportsColors) $formatter = new Tags();
        else $formatter = new PlainText();
        $formatter->setStyles($this->styles);
        $formatter->setTerminal($this->terminal);
        $this->formatter = $formatter;
    }

    ////////////////////////////   Magics   ////////////////////////////

    /**
     * Outputs a message
     * @param string $message
     * @return static
     */
    public function __invoke(string $message) {
        return $this->out($message);
    }

    /**
     * {@inheritdoc}
     * @throws InvalidArgumentException
     * @throws BadMethodCallException
     */
    public function __call($method, $arguments) {

        if ($this->styles->offsetExists($method)) {
            $message = null;
            if (array_key_exists(0, $arguments)) {
                $message = $arguments[0];
                if (
                        !is_string($message)
                        and $message !== null
                ) {
                    throw new InvalidArgumentException(sprintf('%s::%s($message) Invalid argument $message, string|null requested but %s given.', get_class($this), $method, gettype($message)));
                }
            }
            if ($this->supportsColors) {
                if (is_string($message)) $message = $this->styles[$method]->format($message);
                else $message = $this->styles[$method]->getPrefix();
                return $this->write($message);
            } elseif (is_string($message)) return $this->write($message);
            else return $this; //no text to render and no color support, so do nothing
        }


        throw new BadMethodCallException(sprintf('%s::%s() does not exists.', get_class($this), $method));
    }

    ////////////////////////////   GETTERS   ////////////////////////////

    /**
     * Get Terminal Infos
     * @return Terminal
     */
    public function getTerminal(): Terminal {
        return $this->terminal;
    }

    /**
     * Get Inputs
     * @return array<string,STDIO\Interfaces\Input>
     */
    public function getInputs() {
        return $this->inputs;
    }

    /**
     * Get Outputs
     * @return array<string,STDIO\Interfaces\Output>
     */
    public function getOutputs() {
        return $this->outputs;
    }

    /**
     * Get Buffer
     * @return Buffer
     */
    public function getBuffer(): Buffer {
        return $this->buffer;
    }

    /**
     * Get Styles
     * @return Styles
     */
    public function getStyles(): Styles {
        return $this->styles;
    }

    /**
     * Ger Current Formatter
     * @return Formatter
     */
    public function getFormatter(): Formatter {
        return $this->formatter;
    }

    /**
     * Get Input by name
     * @return Input|null
     */
    public function getInput(string $index = 'in'): ?Input {
        return $this->inputs[$index] ?? null;
    }

    /**
     * Get Output by name
     * @param string $index
     * @return Output|null
     */
    public function getOutput(string $index = 'out'): ?Output {
        return $this->outputs[$index] ?? null;
    }

    /**
     * Get STDERR
     * @return StreamOutput
     */
    public function getSTDERR(): StreamOutput {
        return $this->outputs['err'];
    }

    /**
     * Get STDOUT
     * @return StreamOutput
     */
    public function getSTDOUT(): StreamOutput {
        return $this->outputs['out'];
    }

    ////////////////////////////   Read and Write   ////////////////////////////

    /**
     * Prompt for a value
     * @suppress PhanTypeMismatchReturnNullable
     * @param string $prompt
     * @return string
     */
    public function prompt(string $prompt): string {

        $result = null;
        do {
            $this->buffer->clear();
            $this->write($prompt)->write(' ')->out();
            $lines = $this->getInput()->read();
            $line = $lines[0];
            if (!empty($line)) $result = $line;
        } while ($result === null);

        return $result;
    }

    /**
     * Prompt for a confirmation
     * @suppress PhanTypeMismatchReturnNullable
     * @param string $prompt
     * @param bool $default
     * @return bool
     */
    public function confirm(
            string $prompt,
            bool $default = false
    ): bool {
        $yes = ["yes", "y"];
        $no = ["no", "n"];

        $ynprompt = $default === true ? ' [Y/n]: ' : ' [y/N]: ';

        $result = null;
        do {
            $this->buffer->clear();
            $this->write($prompt)->write($ynprompt)->out();
            $response = $this->getInput()->read();
            $line = $response[0];
            if (empty($line)) $result = $default;
            else {
                $line = strtolower($line);
                if (in_array($line, $yes)) $result = true;
                elseif (in_array($line, $no)) $result = false;
            }
        } while (!is_bool($result));

        return $result;
    }

    /**
     * Adds Message to the Buffer
     * @param string $message
     * @return static
     */
    public function write(string $message): self {
        $message = $this->formatter->format($message);
        $this->buffer->write($message);
        return $this;
    }

    /**
     * Adds Line to the Buffer
     * @param string $message
     * @return static
     */
    public function writeln(string $message): self {
        $this->write($message);
        $this->buffer->write("\n");
        return $this;
    }

    /**
     * Output the Buffer
     * @param string|null $message
     * @return static
     */
    public function out(?string $message = null): self {
        if (is_string($message)) {
            $this->buffer->clear();
            $this->writeln($message);
        }
        if ($this->supportsColors) $this->buffer->write($this->styles->reset->getSuffix());
        $this->buffer->flush($this->getSTDOUT());
        return $this;
    }

    /**
     * Output the Buffer to STDERR
     * @param string|null $message
     * @return static
     */
    public function err(?string $message = null): self {
        if (is_string($message)) {
            $this->buffer->clear();
            $this->writeln($message);
        }
        if ($this->supportsColors) $this->buffer->write($this->styles['reset']->getSuffix());
        $this->buffer->flush($this->getSTDERR());
        return $this;
    }

    ////////////////////////////   Utils  ////////////////////////////

    /**
     * Draw a Rectangle
     *
     * @param string $message
     * @param string $backgroundColor
     * @param string $color
     */
    public function drawRect(string $message, string $backgroundColor = 'green', string $color = 'white'): self {
        $rect = $this->createRect();
        $rect->setBackground($backgroundColor);
        $rect->setColor($color);
        $rect->out($message);
        return $this;
    }

    /**
     * Creates Rectangle Object
     * @return Rect
     */
    public function createRect(): Rect {
        return new Rect($this);
    }

    /**
     * Creates a Formated Progress Bar
     * @param int $total
     * @param string|null $label
     * @param callable|null $onComplete
     * @return Progress
     */
    public function createProgressBar(int $total = 100, string $label = null, ?callable $onComplete = null): Progress {
        $progress = new Progress($total, $this);
        $progress
                ->setTotal($total)
                ->setLabel($label ?? '', 'green');

        if (is_callable($onComplete)) $progress->onComplete($onComplete);

        return $progress
                        ->setStatusColor('yellow')
                        ->setBarColor('cyan')
                        ->setPercentageColor('white');
    }

    ////////////////////////////   Special Features   ////////////////////////////

    /**
     * Insert tabulations into buffer
     * @param int $count
     * @return static
     */
    public function tab(int $count = 1) {
        if ($count > 0) $this->write(str_repeat("\t", $count));
        return $this;
    }

    /**
     * Insert Spaces into Buffer
     * @param int $count
     * @return static
     */
    public function space(int $count = 1) {
        if ($count > 0) $this->write(str_repeat(" ", $count));
        return $this;
    }

    /**
     * Insert Line break into buffer
     * @param int $count
     * @return static
     */
    public function linebreak(int $count = 1) {
        if ($count > 0) $this->write(str_repeat("\n", $count));
        return $this;
    }

    /**
     * Adds \r to the buffer
     * @return static
     */
    public function returnStartOfLine() {
        $this->write("\r");
        return $this;
    }

    /**
     * Reset The Styles
     * @return static
     */
    public function reset() {
        $this->write("\033[0m");
        return $this;
    }

    /**
     * Clears the entire line
     * @return static
     */
    public function clearLine() {
        if ($this->supportsColors) $this->write("\033[2K");
        return $this;
    }

    /**
     * Clears From the cursor to the start of the line
     * @return static
     */
    public function clearStartOfLine() {
        if ($this->supportsColors) $this->write("\033[1K");
        return $this;
    }

    /**
     * Clears From the cursor to the end of the line
     * @return static
     */
    public function clearEndOfLine() {
        if ($this->supportsColors) $this->write("\033[K");
        return $this;
    }

}

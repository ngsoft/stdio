<?php

namespace NGSOFT;

use NGSOFT\STDIO\{
    Inputs\StreamInput, Interfaces\Buffer, Interfaces\Formatter, Interfaces\Input, Interfaces\Output, Outputs\OutputBuffer,
    Outputs\StreamOutput, Styles, Terminal
};

final class STDIO {

    const VERSION = '2.0';

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

    public function __construct() {
        $this->terminal = new Terminal();
        $this->supportsColors = $this->terminal->hasColorSupport();
        $this->inputs['in'] = new StreamInput();
        $stdout = new StreamOutput();
        $this->outputs['out'] = $stdout;
        $this->outputs['err'] = $stdout->withStream(fopen('php://stderr', 'w'));
        $this->buffer = new OutputBuffer();
        $this->styles = Styles::create();

        if ($this->supportsColors) $formatter = new STDIO\Formatters\Tags();
        else $formatter = new STDIO\Formatters\PlainText();
        $formatter->setStyles($this->styles);
        $formatter->setTerminal($this->terminal);
        $this->formatter = $formatter;
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

    ////////////////////////////   Read and Write   ////////////////////////////

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
        if ($this->supportsColors) $this->buffer->write($this->styles['reset']->getSuffix());
        $this->buffer->flush($this->getOutput('out'));
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
        $this->buffer->flush($this->getOutput('err'));
        return $this;
    }

    ////////////////////////////   Utils  ////////////////////////////


    public function createRect(): STDIO\Utils\Rect {

        $rect = new STDIO\Utils\Rect();
        $rect->setStyles($this->styles);
        return $rect;
    }

}

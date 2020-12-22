<?php

namespace NGSOFT;

use NGSOFT\STDIO\{
    Inputs\StreamInput, Interfaces\Buffer, Interfaces\Formatter, Interfaces\Input, Interfaces\Output, Outputs\OutputBuffer,
    Outputs\StreamOutput, Styles, Terminal
};

class STDIO {

    const VERSION = '2.0';

    /** @var Terminal */
    private $terminal;

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
        $this->inputs['in'] = new StreamInput();
        $stdout = new StreamOutput();
        $this->outputs['out'] = $stdout;
        $this->outputs['err'] = $stdout->withStream(fopen('php://stderr', 'w'));
        $this->buffer = new OutputBuffer();
        $this->styles = Styles::create();
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

}

<?php

namespace NGSOFT;

use NGSOFT\STDIO\{
    Inputs\StreamInput, Interfaces\Buffer, Outputs\OutputBuffer, Outputs\StreamOutput, Terminal
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

    public function __construct() {
        $this->terminal = new Terminal();
        $this->inputs['in'] = new StreamInput();
        $stdout = new StreamOutput();
        $this->outputs['out'] = $stdout;
        $this->outputs['err'] = $stdout->withStream(fopen('php://stderr', 'w'));
        $this->buffer = new OutputBuffer();
        $this->styles = STDIO\Styles::create();
    }

}

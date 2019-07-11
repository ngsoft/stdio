<?php

namespace NGSOFT\Tools\IO\Outputs;

use NGSOFT\Tools\IO\Formatters\Formatter;

class stdOut extends StreamOutput {

    public function __construct(Formatter $formatter = null) {

        $stream = fopen("php://stdout", "w");

        parent::__construct($stream, $formatter);
    }

}

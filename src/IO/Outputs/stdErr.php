<?php

namespace NGSOFT\Tools\IO\Outputs;

class stdErr {

    public function __construct(Formatter $formatter = null) {

        $stream = fopen("php://stderr", "w");

        parent::__construct($stream, $formatter);
    }

}

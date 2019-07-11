<?php

use NGSOFT\Tools\IO\Outputs\StreamOutput;

namespace NGSOFT\Tools\IO\Outputs;

class stdOut extends StreamOutput {

    public function __construct() {

        parent::__construct(fopen("php://stdout", "w"));
    }

}

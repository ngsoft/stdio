<?php

namespace NGSOFT\Tools\IO\Outputs;

class STDOUT extends StreamOutput {

    public function __construct() {

        parent::__construct(fopen("php://stdout", "w"));
    }

}

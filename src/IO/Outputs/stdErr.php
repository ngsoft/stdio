<?php

namespace NGSOFT\Tools\IO\Outputs;

class stdErr extends StreamOutput {

    public function __construct() {
        parent::__construct(fopen("php://stderr", "w"));
    }

}

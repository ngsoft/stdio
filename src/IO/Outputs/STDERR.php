<?php

declare(strict_types=1);

namespace NGSOFT\Tools\IO\Outputs;

class STDERR extends StreamOutput {

    public function __construct() {
        parent::__construct(fopen("php://stderr", "w"));
    }

}

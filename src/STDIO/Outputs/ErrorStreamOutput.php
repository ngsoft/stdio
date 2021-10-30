<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Outputs;

class ErrorStreamOutput extends StreamOutput {

    public function __construct() {
        $this->stream = fopen('php://stderr', 'w');
    }

}

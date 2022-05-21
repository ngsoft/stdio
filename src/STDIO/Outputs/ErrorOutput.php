<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Outputs;

class ErrorOutput extends Output {

    public function __construct() {
        $this->stream = fopen('php://stderr', 'w');
    }

}

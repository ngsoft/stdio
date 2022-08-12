<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Outputs;

use NGSOFT\STDIO\Formatters\Formatter;

class ErrorOutput extends Output
{

    public function __construct(?Formatter $formatter = null)
    {
        $this->stream = fopen('php://stderr', 'w+');
        parent::__construct($formatter);
    }

}

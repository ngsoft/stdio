<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Outputs;

use NGSOFT\STDIO\Formatters\{
    FormatterInterface, TagFormatter
};

class ErrorOutput extends Output
{

    public function __construct(FormatterInterface $formatter = null)
    {
        $this->formatter = $formatter ?? new TagFormatter();
        $this->stream = fopen('php://stderr', 'w+');
    }

}

<?php

namespace NGSOFT\STDIO\Styles;

use NGSOFT\STDIO\{
    Interfaces\Formatter, Styles
};

class Style implements Formatter {

    private $prefix = [];
    private $suffix = [Styles::STYLE_RESET];

    /** {@inheritdoc} */
    public function format(string $message): string {
        return $message;
    }

}

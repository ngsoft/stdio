<?php

declare(strict_types=1);

namespace NGSOFT\Tools\IO\Formatters;

class PlainTextFormatter extends Formatter {

    public function format(string $message): string {
        $message = strip_tags($message);
        $message = str_replace(['&gt;', '&lt;', '{:', ':}'], ['>', '<', '', ''], $message);
        return strip_tags($message);
    }

}

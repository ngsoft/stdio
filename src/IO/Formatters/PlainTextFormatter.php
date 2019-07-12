<?php

declare(strict_types=1);

namespace NGSOFT\Tools\IO\Formatters;

class PlainTextFormatter extends Formatter {

    public function format(string $message): string {
        return strip_tags($message);
    }

}

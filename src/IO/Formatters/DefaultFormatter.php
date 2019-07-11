<?php

namespace NGSOFT\Tools\IO\Formatters;

class DefaultFormatter extends Formatter {

    public function format(string $message) {
        return strip_tags($message);
    }

}

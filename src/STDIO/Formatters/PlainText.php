<?php

namespace NGSOFT\STDIO\Formatters;

class PlainText extends Tags {

    /** {@inheritdoc} */
    public function format(string $message): string {
        return strip_tags($message);
    }

}

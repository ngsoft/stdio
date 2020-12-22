<?php

namespace NGSOFT\STDIO\Formatters;

class PlainText extends Tags {

    /** {@inheritdoc} */
    public function format(string $message): string {
        $message = $this->specials->format($message);
        $message = strip_tags($message); //removes not managed tags
        $message = str_replace(array_keys($this->replacements), array_values($this->replacements), $message);
        return $message;
    }

}

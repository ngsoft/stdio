<?php

namespace NGSOFT\Tools\IO\Formatters;

abstract class Formatter {

    /**
     * Formats a message according to the given styles.
     */
    abstract public function format(string $message);
}

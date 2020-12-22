<?php

namespace NGSOFT\STDIO\Interfaces;

use NGSOFT\STDIO\{
    Styles, Terminal
};

interface Formatter {

    /**
     * Format a messsage
     * @param string $message
     * @return string
     */
    public function format(string $message): string;

    /**
     * Styles to use
     * @param Styles $styles
     */
    public function setStyles(Styles $styles);

    /**
     * Insert the terminal
     * @param Terminal $terminal
     */
    public function setTerminal(Terminal $terminal);
}

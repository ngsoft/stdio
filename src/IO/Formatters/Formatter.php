<?php

namespace NGSOFT\Tools\IO\Formatters;

use NGSOFT\Tools\Interfaces\{
    FormatterInterface, StyleSheetInterface
};

abstract class Formatter implements FormatterInterface {

    /** @var StyleSheetInterface */
    protected $stylesheet;

    public function setStyleSheet(StyleSheetInterface $stylesheet) {
        $this->stylesheet = $stylesheet;
    }

}

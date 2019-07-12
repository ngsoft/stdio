<?php

declare(strict_types=1);

namespace NGSOFT\Tools\Interfaces;

interface FormatterInterface {

    /**
     * Formats a message.
     */
    public function format(string $message): string;

    /**
     * Set the Stylesheet
     * @param StyleSheetInterface $stylesheet
     */
    public function setStyleSheet(StyleSheetInterface $stylesheet);
}

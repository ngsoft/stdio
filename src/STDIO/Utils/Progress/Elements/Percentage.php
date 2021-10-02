<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils\Progress\Elements;

use NGSOFT\STDIO\Utils\Progress\{
    Element, ProgressElement
};

class Percentage extends ProgressElement {

    protected function build(Element $element): Element {
        $percent = (string) $this->getPercent();
        while (strlen($percent) < 3) {
            $percent .= ' ';
        }
        $percent .= "%";
        return $element->setValue($percent);
    }

}

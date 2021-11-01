<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils\Progress\Elements;

use NGSOFT\STDIO\Utils\Progress\{
    Element, ProgressElement
};

class Status extends ProgressElement {

    protected function build(Element $element): Element {
        $total = (string) $this->total;
        $current = (string) $this->current;
        while (strlen($current) < strlen($total)) {
            $current = " $current";
        }
        $str = sprintf('[%s/%s]', $current, $total);
        return $element->setValue($str);
    }

}

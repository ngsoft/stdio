<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Utils\Progress\Elements;

use NGSOFT\STDIO\Utils\Progress\{
    Element, ProgressElement
};

class Bar extends ProgressElement {

    // Style
    const ICON_PROGRESS = "▓";
    const ICON_DONE = "█";
    const ICON_LEFT = "░";
    const ICON_BORDER = "|";

    protected function build(Element $element): Element {
        $percent = $this->getPercent();
        $done = (int) floor($percent / 2);
        $len = 50 - $done;

        $str = self::ICON_BORDER;
        if ($done > 0) $str .= str_repeat(self::ICON_DONE, $done);
        if ($len > 0) $str .= str_repeat(self::ICON_PROGRESS, $len);
        $str .= self::ICON_BORDER;
        return $element->setValue($str);
    }

}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use NGSOFT\STDIO\Utils\Utils;

class RGBColor extends HexColor
{

    protected function setValue(string $value, bool $isBackgroundColor, bool $isGray): void
    {
        $this->name = $value;
        $this->value = Utils::convertRgbToAnsi($value, $isBackgroundColor, $isGray);
    }

}

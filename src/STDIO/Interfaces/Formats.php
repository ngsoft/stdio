<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Interfaces;

interface Formats {

    const RESET = 0;
    const BOLD = 1;
    const DIM = 2;
    const ITALIC = 3;
    const UNDERLINE = 4;
    const INVERSE = 7;
    const HIDDEN = 8;
    const STRIKETROUGH = 9;
    const FORMAT_UNSET = [
        0 => 0, 1 => 22, 2 => 22, 3 => 23,
        4 => 24, 7 => 27, 8 => 28, 9 => 29
    ];
    const FORMAT_VALID = [
        0, 1, 2, 3, 4, 7, 8, 9,
    ];

}

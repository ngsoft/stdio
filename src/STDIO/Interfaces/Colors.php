<?php

namespace NGSOFT\STDIO\Interfaces;

interface Colors {

    const BLACK = 30;
    const RED = 31;
    const GREEN = 32;
    const YELLOW = 33;
    const BLUE = 34;
    const PURPLE = 35;
    const CYAN = 36;
    const WHITE = 37;
    const GRAY = 90;
    const BRIGHTBLACK = 90;
    const BRIGHTRED = 91;
    const BRIGHTGREEN = 92;
    const BRIGHTYELLOW = 93;
    const BRIGHTBLUE = 94;
    const BRIGHTPURPLE = 95;
    const BRIGHTCYAN = 96;
    const BRIGHTWHITE = 97;
    const COLOR_UNSET = [
        30 => 39, 31 => 39, 32 => 39, 33 => 39, 34 => 39, 35 => 39, 36 => 39, 37 => 39,
        90 => 39, 91 => 39, 92 => 39, 93 => 39, 94 => 39, 95 => 39, 96 => 39, 97 => 39,
    ];
    const COLOR_VALID = [
        30, 31, 32, 33, 34, 35, 36, 37,
        90, 91, 92, 93, 94, 95, 96, 97,
    ];
    const BACKGROUND_COLOR_MODIFIER = 10;

}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers\ProgressBar;

class Bar extends Element
{

    protected const BAR_PROGRESS = ['━', '-'];
    protected const BAR_LEFT = ['╺', ' '];
    protected const BAR_RIGHT = ['╸', ''];

    protected function update(): string
    {

    }

    protected function getLength(): int
    {

    }

}

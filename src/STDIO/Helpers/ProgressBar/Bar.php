<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers\ProgressBar;

class Bar extends Element
{

    protected const BAR_PROGRESS = ['━', '-'];
    protected const BAR_LEFT = ['╺', ' '];
    protected const BAR_RIGHT = ['╸', ''];

    public function getLength(): int
    {
        return 28;
    }

    public function update(): void
    {

    }

}

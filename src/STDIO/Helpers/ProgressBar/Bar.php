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

        $result = &$this->value;
        $result = '';

        $halves = (int) ($this->getPercent() * 2 * $this->getLength());

        $bar_count = (int) floor($halves / 2);
        $half_count = $halves % 2;

        if ($bar_count) {
            str_repeat(self::BAR_PROGRESS[0], $bar_count);
        }
        if ($half_count) {
            str_repeat(self::BAR_RIGHT[0], $half_count);
        }


        $remain = $this->getLength() - $bar_count - $half_count;
    }

}

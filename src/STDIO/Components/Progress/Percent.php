<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Components\Progress;

class Percent extends \NGSOFT\STDIO\Components\Element
{

    public function __construct(
            protected int $total = 100,
            protected int $current = 0
    )
    {

    }

    public function setTotal(int $total)
    {
        $this->total = $total;
        return $this;
    }

    public function setCurrent(int $current)
    {
        $this->current = $current;

        $percent = (string) $this->getPercent();

        while (strlen($percent) < 3) {
            $percent = " {$percent}";
        }

        $this->setValue($percent);

        return $this;
    }

    public function getPercent(): int
    {
        $percent = (int) floor(($this->current / $this->total) * 100);
        if ($percent > 100) $percent = 100;
        return $percent;
    }

}

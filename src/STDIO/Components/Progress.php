<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Components;

class Progress extends Element
{

    protected Progress\BarElement $bar;
    protected Progress\Percent $percent;

    public function __construct(
            protected int $total = 100,
            protected int $current = 0
    )
    {
        $this->bar = new Progress\BarElement($total, $current);
        $this->percent = new Progress\Percent($total, $current);
        $this->appendChild($this->bar);
    }

}

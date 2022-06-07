<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Components;

class Progress extends Element implements ProgressElement
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
        $this
                ->appendChild($this->bar)
                ->appendChild($this->percent);
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getCurrent(): int
    {
        return $this->current;
    }

    public function setTotal(int $total): static
    {
        $this->total = $total;
        $this->setCurrent(0);
        return $this;
    }

    public function setCurrent(int $current): static
    {
        $this->current = min($current, $this->total);
        return $this;
    }

}

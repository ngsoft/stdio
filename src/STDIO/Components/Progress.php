<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Components;

use NGSOFT\STDIO\{
    Components\Progress\BarElement, Components\Progress\Percent, Cursor, Outputs\Output
};

class Progress extends Element implements ProgressElement
{

    protected Cursor $cursor;
    protected BarElement $bar;
    protected Percent $percent;

    public function __construct(
            protected ?Output $output = null,
            protected int $total = 100,
            protected int $current = 0
    )
    {
        $this->output = $output ?? new Output();
        $this->cursor = new Cursor($this->output);
        $this->bar = new BarElement($total, $current);
        $this->percent = new Percent($total, $current);
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

        foreach ($this->children as $element) {
            if ($element instanceof ProgressElement) {
                $element->setTotal($total);
            }
        }


        $this->setCurrent(0);
        return $this;
    }

    public function setCurrent(int $current): static
    {
        $current = min($current, $this->total);

        $this->current = $current;

        foreach ($this->children as $element) {
            if ($element instanceof ProgressElement) {
                $element->setCurrent($current);
            }
        }
        return $this;
    }

    public function isComplete(): bool
    {
        return $this->total === $this->current;
    }

}

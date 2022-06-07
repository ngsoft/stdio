<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Components\Progress;

class BarElement extends \NGSOFT\STDIO\Components\Element implements ProgressElement
{

    protected const ICON_PROGRESS = "▓";
    protected const ICON_DONE = "█";
    protected const ICON_LEFT = "░";
    protected const ICON_BORDER = "|";

    public function __construct(
            protected int $total = 100,
            protected int $current = 0
    )
    {
        $this->setCurrent($current);
    }

    public function setTotal(int $total)
    {
        $this->total = $total;
        $this->setCurrent(0);
        return $this;
    }

    public function setCurrent(int $current): static
    {
        $this->current = min($current, $this->total);

        $percent = $this->getPercent();
        $done = (int) floor($percent / 2);
        $left = 50 - $done;

        $content = self::ICON_BORDER;
        for ($i = 0; $i < $done; $i++) {
            $content .= self::ICON_DONE;
        }

        for ($i = 0; $i < $left; $i++) {
            $content .= self::ICON_LEFT;
        }


        $content .= self::ICON_BORDER;

        $this->setValue($content);

        return $this;
    }

    public function getPercent(): int
    {
        $percent = (int) floor(($this->current / $this->total) * 100);
        if ($percent > 100) $percent = 100;
        return $percent;
    }

}

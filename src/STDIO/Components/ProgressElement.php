<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Components;

interface ProgressElement
{

    public function setTotal(int $total): static;

    public function setCurrent(int $current): static;

    public function isComplete(): bool;
}

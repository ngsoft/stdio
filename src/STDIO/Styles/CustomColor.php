<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

interface CustomColor
{

    public function getName(): string;

    public function getValue(): int|string;

    public function getUnsetValue(): int|string;
}

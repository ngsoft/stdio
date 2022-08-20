<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

interface CustomColor
{

    /**
     * label
     */
    public function getName(): string;

    /**
     * Format code
     */
    public function getValue(): int|string;

    /**
     * Unset format code
     */
    public function getUnsetValue(): int|string;
}

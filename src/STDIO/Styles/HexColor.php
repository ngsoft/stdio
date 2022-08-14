<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use NGSOFT\STDIO\Utils\Utils;

/**
 * Converts Hex Color to ansi
 */
class HexColor
{

    protected string $name;
    protected string $value;
    protected bool $isBackgroundColor = false;

    public function __construct(
            string $name,
            bool $isBackgroundColor = false,
            bool $isGray = false
    )
    {
        $this->isBackgroundColor = $isBackgroundColor;
        $this->setValue($name, $isBackgroundColor, $isGray);
    }

    protected function setValue(string $value, bool $isBackgroundColor, bool $isGray): void
    {
        $this->name = '#' . ltrim($value, '#');
        $this->value = Utils::convertHexToAnsi($value, $isBackgroundColor, $isGray);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getUnsetValue(): int
    {
        return $this->isBackgroundColor ? 49 : 39;
    }

}

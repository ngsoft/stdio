<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use NGSOFT\STDIO\Enums\{
    BackgroundColor, Color
};

/**
 * Bright background/colors decorator
 */
class BrightColor
{

    protected bool $isBackgroundColor = false;

    public function __construct(
            protected Color|BackgroundColor $color
    )
    {
        $this->isBackgroundColor = $color instanceof BackgroundColor;
    }

    public function getFormatName(): string
    {
        return $this->color->getFormatName() . ':bright';
    }

    public function getTag(): string
    {
        return $this->color->getTag() . ':bright';
    }

    public function getName(): string
    {
        return $this->color->getName();
    }

    public function getValue(): int
    {
        return $this->color->getValue() + 60;
    }

    public function getUnsetValue(): int
    {
        return $this->isBackgroundColor ? 49 : 39;
    }

}

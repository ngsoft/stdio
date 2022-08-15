<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Styles;

use NGSOFT\STDIO\{
    Enums\BackgroundColor, Enums\Color, Utils\Utils
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

    public function getValue(): int|string
    {

        if (Utils::getNumColorSupport() === 8) {
            return sprintf('1;%d', $this->color->getValue());
        }

        return $this->color->getValue() + 60;
    }

    public function getUnsetValue(): int|string
    {
        $value = $this->isBackgroundColor ? 49 : 39;

        if (Utils::getNumColorSupport() === 8) {
            return sprintf('22;%d', $value);
        }

        return $value;
    }

}

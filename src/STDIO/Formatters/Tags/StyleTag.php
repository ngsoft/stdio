<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\STDIO\{
    Formatters\Tag, Styles\Style, Utils\Utils
};

class StyleTag extends Tag
{

    public function format(string $message): string
    {
        return $this->getStyle()->format($message);
    }

    public function getPriority(): int
    {
        return 2;
    }

    public function managesAttributes(array $attributes): bool
    {

        static $formats, $custom = ['gs', 'grayscale'];

        $formats ??= $this->styles->getFormats();

        foreach (array_keys($attributes) as $attr) {


            if (Utils::isHexColor($attr) || Utils::isRGBColor($attr)) {
                continue;
            }

            if (isset($this->styles[$attr]) || isset($formats[$attr]) || in_array($attr, $custom)) {
                continue;
            }
            return false;
        }

        return ! empty($attributes);
    }

    public function getStyle(): Style
    {
        if ( ! $this->style) {
            $label = $this->getCode();

            if (isset($this->styles[$label])) {
                return $this->style = $this->styles[$label];
            }

            $this->style = $this->styles->createStyleFromParams($this->attributes, $label);
        }
        return $this->style;
    }

}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\STDIO\{
    Formatters\Tag, Styles\Style
};

class StyleTag extends Tag
{

    public function format(string $message): string
    {
        return $this->getStyle()->format($message, $this->styles->colors);
    }

    public function getPriority(): int
    {
        return 2;
    }

    public function isSelfClosing(): bool
    {
        return false;
    }

    public function managesAttributes(array $attributes): bool
    {

        static $formats, $custom = ['gs', 'grayscale'];

        $formats ??= $this->styles->getFormats();

        foreach (array_keys($attributes) as $attr) {

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

            $this->styles->addStyle(
                    $this->style = $this->styles->createStyleFromAttributes($this->attributes, $label)
            );
        }
        return $this->style;
    }

}

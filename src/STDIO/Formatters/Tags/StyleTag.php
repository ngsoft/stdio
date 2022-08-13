<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\STDIO\Formatters\Tag;

class StyleTag extends Tag
{

    public function format(string $message): string
    {
        return $this->getStyle()->format($message, $this->styles->colors);
    }

    public function isSelfClosing(): bool
    {
        return false;
    }

    public function managesAttributes(array $attributes): bool
    {

        foreach (array_keys($attributes) as $attr) {
            if (isset($this->styles[$attr]) || isset($this->styles->getFormats()[$attr])) {
                return true;
            }
        }

        return false;
    }

}

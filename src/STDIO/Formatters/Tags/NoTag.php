<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\STDIO\{
    Formatters\Tag, Styles\Style
};

/**
 * Fallback Tag
 */
class NoTag extends Tag
{

    public function format(string $message): string
    {
        return $message;
    }

    public function isSelfClosing(): bool
    {
        return true;
    }

    public function managesAttributes(array $attributes): bool
    {
        return true;
    }

    public function getStyle(): Style
    {
        static $empty;

        return $empty ??= new Style();
    }

}

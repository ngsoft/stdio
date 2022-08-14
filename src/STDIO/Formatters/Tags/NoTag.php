<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\STDIO\{
    Formatters\Tag, Styles\Style
};

/**
 * Fallback Tag
 * @phan-file-suppress PhanUnusedPublicMethodParameter
 */
class NoTag extends Tag
{

    protected bool $selfClosing = true;

    public function getPriority(): int
    {
        // lowest priority
        return 1;
    }

    public function format(string $message): string
    {
        return $message;
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

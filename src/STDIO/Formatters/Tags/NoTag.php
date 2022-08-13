<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\STDIO\Formatters\Tag;

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
        return false;
    }

    public function managesAttributes(array $attributes): bool
    {
        return true;
    }

}

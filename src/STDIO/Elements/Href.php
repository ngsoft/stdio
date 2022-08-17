<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

class Href extends Element
{

    protected static function getTagName(): string
    {
        return strtolower(class_basename(static::class));
    }

    public static function managesAttributes(array $attributes): bool
    {

        var_dump($attributes);
        return preg_test('#^https?:#', $attributes['href'] ?? '');
    }

    public function write(string $contents): void
    {
        var_dump($contents);
        $this->pulled = false;
        $this->message->format($this->getStyle()->format($contents), $contents);
    }

}

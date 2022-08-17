<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

use NGSOFT\{
    Facades\Terminal, STDIO\Enums\Ansi
};
use function class_basename,
             preg_test;

class Href extends Element
{

    protected static function getTagName(): string
    {
        return strtolower(class_basename(static::class));
    }

    public static function managesAttributes(array $attributes): bool
    {
        return preg_test('#^https?://.+/?#', $attributes['href'] ?? '');
    }

    public function write(string $contents): void
    {
        $link = $this->getAttribute('href');
        $this->pulled = false;

        $formatted = sprintf(
                "%s8;;%s%s\\%s%s8;;%s\\",
                Ansi::OSC, $link, Ansi::ESC,
                $contents,
                Ansi::OSC, Ansi::ESC
        );

        if ( ! Terminal::supportsColors()) {
            $formatted = sprintf('[%s] %s', $link, $contents);
        }

        $this->message->format($this->getStyle()->format($formatted), $contents);
    }

}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Entities;

use NGSOFT\{
    Facades\Terminal, STDIO\Enums\Ansi
};
use function preg_test;

class Href extends BuiltinEntity
{

    public static function matches(array $attributes): bool
    {
        return preg_test('#^https?://.+/?#', $attributes['href'] ?? '');
    }

    public function format(string|\Stringable $message): string
    {
        if ( ! static::matches($this->attributes)) {
            return parent::format($message);
        }

        $message = (string) $message;

        $link = $this->getAttribute('href');

        $formatted = sprintf(
                "%s8;;%s%s\\%s%s8;;%s\\",
                Ansi::OSC, $link, Ansi::ESC,
                $message,
                Ansi::OSC, Ansi::ESC
        );

        if ( ! Terminal::supportsColors()) {
            $formatted = $message = sprintf('[%s] %s', $link, $message);
        }


        return $this->getStyle()->format($formatted);
    }

}

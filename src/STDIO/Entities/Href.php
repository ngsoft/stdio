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

    public function write(string $message): void
    {


        if ( ! static::matches($this->attributes)) {

            // render message normally
            parent::write($message);

            return;
        }

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

        $this->children[] = Message::create($message, $this->getStyle())->setFormatted($formatted);
    }

}

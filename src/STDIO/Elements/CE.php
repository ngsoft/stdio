<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

class CE extends CustomElement
{

    protected static function getTagName(): string
    {

        return 'one';
    }

    public function write(string $contents): void
    {
        var_dump(['write' => $contents]);

        $this->text .= ' one ' . $contents;
    }

}

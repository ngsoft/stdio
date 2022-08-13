<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

class BR extends Tag
{

    public function getFormat(array $attributes): string
    {

        if ( ! isset($attributes['br'])) {
            return '';
        }


        $count = 1;
        if (preg_match('#(\d+)#', $attributes['count'][0] ?? $attributes['br'][0] ?? '', $matches)) {
            $count = intval($matches[1]);
        }

        $count = max(1, $count);

        return str_repeat('\n', $count);
    }

    public function getType(): int
    {
        return self::SELF_CLOSING;
    }

}

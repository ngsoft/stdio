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



        if (preg_match('#(\d+)#', $attributes['count'][0] ?? $attributes['br'][0] ?? '', $matches)) {
            $count = intval($matches[1]);
        } else { $count = 1; }

        $count = max(1, $count);

        $str = '';

        for ($i = 0; $i < $count; $i ++) {
            $str .= "\n";
        }


        return $str;
    }

}

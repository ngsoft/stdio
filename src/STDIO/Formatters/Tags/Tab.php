<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

class Tab extends Tag
{

    public function getFormat(array $attributes): string
    {

        if ( ! isset($attributes['tab'])) {
            return '';
        }


        $count = 1;
        if ( ! preg_match('#(\d+)#', $attributes['tab'][0] ?? $attributes['count'][0] ?? '', $matches)) {
            $count = intval($matches[1]);
        }

        $count = max(1, $count);

        return $this->getStyle($attributes)->format(str_repeat('\t', $count), $this->styles->colors);
    }

}

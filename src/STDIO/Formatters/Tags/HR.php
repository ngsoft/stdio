<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\Facades\Terminal;

class HR extends Tag
{

    public function getFormat(array $attributes): string
    {
        if ( ! isset($attributes['hr'])) {
            return '';
        }


        $style = $this->getStyle($attributes);

        $str = '';
        $padding = 4;
        $char = $attributes['hr'][0] ?? $attributes['char'][0] ?? "─";

        if (preg_match('#(\d+)#', $attributes['padding'][0] ?? '', $matches)) {
            $padding = intval($matches[1]);
        }

        $padding = max(0, $padding);

        $width = Terminal::getWidth();

        $width -= $padding * 2;

        $str .= "\n";

        for ($i = 0; $i < $padding; $i ++) {
            $str .= ' ';
        }

        $sub = '';
        $len = mb_strlen($char);
        for ($i = 0; $i < $width; $i = $i + $len) {
            $sub .= $char;
        }


        $str .= $style->format($sub, $this->styles->colors);

        for ($i = 0; $i < $padding; $i ++) {
            $str .= ' ';
        }

        $str .= "\n";

        return $str;
    }

}

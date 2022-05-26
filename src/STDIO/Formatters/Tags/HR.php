<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\STDIO\{
    Formatters\Tag, Terminal
};

class HR extends Tag {

    public function format(array $params): string {
        $width = Terminal::create()->width;
        $width -= 4;
        $str = "\n\t";
        $char = "-";
        if (is_string($params['char'])) $char = $params['char'][0];
        for ($i = 0; $i < $width; $i++) $str .= $char;
        $str .= "\n";

        return $str;
    }

}

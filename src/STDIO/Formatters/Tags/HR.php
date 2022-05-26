<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\STDIO\{
    Formatters\Tag, Terminal
};

class HR extends Tag {

    public function format(string $message, array $params): string {
        $width = Terminal::create()->width;
        $width -= 8;
        $str = "\n\t\t";
        $char = "=";
        if (count($params['char'] ?? []) > 0) $char = $params['char'][0];
        $char = $char[0];
        for ($i = 0; $i < $width; $i++) $str .= $char;
        $str .= "\n";
        return $str;
    }

}

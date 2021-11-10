<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\STDIO\Terminal;

class HR extends TagAbstract {

    /** {@inheritdoc} */
    public function format(array $params): string {
        $char = (isset($params['char']) ? $params['char'] : '-');
        $width = Terminal::create()->width - 4;
        return sprintf("\n  %s\n", str_repeat($char, $width));
    }

}

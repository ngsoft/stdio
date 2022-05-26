<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\STDIO\Formatters\Tag,
    ValueError;

class BR extends Tag {

    public function format(string $message, array $params): string {

        $count = $params['count'][0] ?? '1';
        if (!preg_match('#\d+#', $count)) {
            throw new ValueError(sprintf('Invalid value "%s" for int count argument.', $count));
        }
        $count = max(1, intval($count));

        $str = '';

        for ($i = 0; $i < $count; $i++) {
            $str .= "\n";
        }


        return $str;
    }

}

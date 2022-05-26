<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\STDIO\Formatters\Tag,
    RuntimeException;

class Tab extends Tag {

    public function format(string $message, array $params): string {

        if (isset($params['count'])) {
            $count = $params['count'][0];
            if (!preg_match('/^\d+$/', $count)) {
                throw new RuntimeException(sprintf('Invalid value "%s" for int param count.', $count));
            }
            $count = max(1, intval($count));
        } else $count = 1;


        $str = '';

        for ($i = 0; $i < $count; $i++) {
            $str .= "\t";
        }


        return $str;
    }

}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

/**
 * A <space> tag
 */
class Space extends TagAbstract {

    /** {@inheritdoc} */
    public function format(array $params): string {

        $repeat = max(1, is_numeric($params['repeat'] ?? null) ? intval($params['repeat']) : 1);
        return str_repeat(' ', $repeat);
    }

}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Interfaces;

/**
 * Self closing Format Tag
 */
interface Tag {

    /**
     * @param array $params
     */
    public function format(array $params);
}

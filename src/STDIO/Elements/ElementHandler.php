<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

interface ElementHandler
{

    static function managesAttributes(array $attribute): bool;
}

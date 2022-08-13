<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

class TagStyle extends Tag
{

    public function getFormat(array $attributes): string
    {
        return '';
    }

    public function getType(): int
    {
        return self::HAS_CONTENTS;
    }

}

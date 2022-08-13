<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\STDIO\Formatters\Tag;

class Tab extends Tag
{

    public function format(string $message): string
    {

        if (preg_match('#\d+#', $this->getFirstAttribute('tab') ?? $this->getFirstAttribute('count') ?? '', $matches)) {
            $count = intval($matches[0]);
        }

        $message = str_repeat("\t", max(1, $count ?? 1));

        return $message;
    }

    public function isSelfClosing(): bool
    {

        return true;
    }

}

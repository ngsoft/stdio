<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\STDIO\Formatters\Tag;

/**
 * @phan-file-suppress PhanUnusedPublicMethodParameter
 */
class Tab extends Tag
{

    protected bool $selfClosing = true;

    public function format(string $message): string
    {

        if (preg_match('#\d+#', $this->getFirstAttribute('tab') ?? $this->getFirstAttribute('count') ?? '', $matches)) {
            $count = intval($matches[0]);
        }

        $message = str_repeat("\t", max(1, $count ?? 1));

        return $message;
    }

}

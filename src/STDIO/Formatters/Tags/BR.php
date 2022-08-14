<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

/**
 * @phan-file-suppress PhanUnusedPublicMethodParameter
 */
class BR extends \NGSOFT\STDIO\Formatters\Tag
{

    protected bool $selfClosing = true;

    public function format(string $message): string
    {


        if (preg_match('#\d+#', $this->getFirstAttribute('br') ?? $this->getFirstAttribute('count') ?? '', $matches)) {
            $count = intval($matches[0]);
        }


        return str_repeat("\n", max(1, $count ?? 1));
    }

}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

class BR extends \NGSOFT\STDIO\Formatters\Tag
{

    public function format(string $message): string
    {


        if (preg_match('#\d+#', $this->getFirstAttribute('br') ?? $this->getFirstAttribute('count') ?? '', $matches)) {
            $count = intval($matches[0]);
        }


        $message = str_repeat("\n", max(1, $count ?? 1));

        return $message;
    }

    public function isSelfClosing(): bool
    {
        return true;
    }

}

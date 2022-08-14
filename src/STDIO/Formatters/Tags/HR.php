<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\{
    Facades\Terminal, STDIO\Formatters\Tag
};

/**
 * @phan-file-suppress PhanUnusedPublicMethodParameter
 */
class HR extends Tag
{

    protected bool $selfClosing = true;

    public function format(string $message): string
    {

        $char = $this->getFirstAttribute('hr') ?? $this->getFirstAttribute('char');
        if (empty($char)) {
            $char = '─';
        }

        if (preg_match('#\d+#', $this->getFirstAttribute('padding') ?? '', $matches)) {
            $padding = intval($matches[0]);
        }

        $padding = max(0, $padding ?? 4);

        $width = Terminal::getWidth();

        $width -= $padding * 2;

        $pad = '';
        for ($i = 0; $i < $padding; $i ++) {
            $pad .= ' ';
        }

        $sub = '';
        $len = mb_strlen($char);

        for ($i = 0; $i < $width; $i += $len) {
            if ($i + $len >= $width) {
                $sub .= mb_substr($char, 0, max(0, $width - $i));
                break;
            }
            $sub .= $char;
        }


        $message = "\n";
        $message .= $pad . $this->getStyle()->format($sub, $this->styles->colors) . $pad;
        $message .= "\n";
        return $message;
    }

}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements\Custom;

use NGSOFT\STDIO\{
    Elements\CustomElement, Helpers\Rectangle as HelperRect
};

class Rect extends CustomElement
{

    protected ?HelperRect $rect = null;

    public function getRect(): HelperRect
    {
        return $this->rect ??= HelperRect::createFromElement($this);
    }

    public function onPull(): void
    {

    }

    public function write(string $contents): void
    {
        $this->message->format($contents, $contents);
    }

    public function pull(): string
    {

        $raw = '';
        foreach ($this->children as $elem) {
            $raw .= $elem->text;
        }
        $raw .= $this->text;

        $text = parent::pull();

        if (empty($text) || $this->isClone) {
            return $text;
        }

        return $this->getRect()->format($text, $raw);
    }

}

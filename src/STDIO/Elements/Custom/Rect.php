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

        $raw = $this->getRaw();

        $formated = $this->getFormated();
    }

    public function write(string $contents): void
    {
        $this->pulled = false;
        $this->message->format($contents, $contents);
    }

}

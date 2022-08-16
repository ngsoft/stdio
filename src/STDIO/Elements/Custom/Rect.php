<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements\Custom;

use NGSOFT\STDIO\{
    Elements\CustomElement, Helpers\Rect as HelperRect
};

class Rect extends CustomElement
{

    protected ?HelperRect $rect = null;

    public function write(string $contents): void
    {
        parent::write($this->getRect()->format($contents));
    }

    public function getRect(): HelperRect
    {
        return $this->rect ??= HelperRect::createFromElement($this);
    }

}

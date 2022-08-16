<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements\Custom;

use NGSOFT\STDIO\{
    Elements\CustomElement, Helpers\Rect as HelperRect
};

class Rect extends CustomElement
{

    protected ?HelperRect $rect = null;
    protected $isClone = false;

    public function getRect(): HelperRect
    {
        return $this->rect ??= HelperRect::createFromElement($this);
    }

    public function pull(): string
    {
        $text = parent::pull();

        if (empty($text) || $this->isClone) {
            return $text;
        }

        var_dump($text);
        return $this->getRect()->format($text);
    }

    public function __clone(): void
    {
        parent::__clone();
        $this->isClone = true;
    }

}

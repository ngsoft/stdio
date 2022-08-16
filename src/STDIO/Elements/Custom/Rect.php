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
        parent::write($this->rect->format($contents));
    }

    protected function getRect(): HelperRect
    {

        if ( ! $this->rect) {

            $length = $this->getAttribute('length');
            $padding = $this->getAttribute('padding');
            $margin = $this->getAttribute('margin');

            $rect = HelperRect::create($this->styles);
        }
        return $this->rect;
    }

}

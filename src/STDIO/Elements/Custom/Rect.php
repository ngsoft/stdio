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

    protected function getRect(): HelperRect
    {

        if ( ! $this->rect) {

            $length = $this->getAttribute('length');
            $padding = $this->getAttribute('padding');
            $margin = $this->getAttribute('margin');

            $this->rect = $rect = HelperRect::create($this->styles);

            if ($this->hasAttribute('center')) {
                $rect->setCenter($this->getAttribute('center') !== false);
            }

            is_int($length) && $rect->setLength($length);
            is_int($padding) && $rect->setPadding($padding);
            is_int($margin) && $rect->setMargin($margin);
            if ($length === 'auto') {
                $rect->autoSetLength();
            }

            $style = $this->getStyle();
            if ( ! $style->isEmpty()) {
                $rect->setStyle($style);
            }
        }
        return $this->rect;
    }

}

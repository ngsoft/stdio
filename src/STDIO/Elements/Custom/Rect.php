<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements\Custom;

use NGSOFT\STDIO\{
    Elements\CustomElement, Helpers\Rectangle
};
use RuntimeException;

class Rect extends CustomElement
{

    protected ?Rectangle $rect = null;

    public function getRect(): Rectangle
    {
        return $this->rect ??= Rectangle::createFromElement($this);
    }

    public function onPop(): void
    {
        if ($this->isClone) {
            return;
        }

        $this->cache = null;

        $this->getFormated();
    }

    protected function update(): void
    {
        $this->cache = null;
    }

    public function onPull(): void
    {

        if ($this->isClone) {
            return;
        }


        $formated = $this->getFormated();
        $raw = $this->getRaw();

        foreach ($this->children as $elem) {

            if ($elem instanceof self && ! $elem->isClone) {
                throw new RuntimeException(sprintf('Cannot put a Rectangle inside another Rectangle.'));
            }

            $this->removeChild($elem);
        }



        $this->message->format($this->getRect()->format($formated, $raw), $raw);
    }

    public function write(string $contents): void
    {
        $this->cache = null;
        $this->pulled = false;
        $this->message->format($contents, $contents);

        $this->getFormated();
    }

}

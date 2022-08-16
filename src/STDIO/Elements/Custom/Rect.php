<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements\Custom;

use NGSOFT\STDIO\{
    Elements\CustomElement, Helpers\Rectangle
};

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
    }

    public function onPull(): void
    {

        if ($this->isClone) {
            return;
        }

        $raw = $this->getRaw();
        $formated = $this->getFormated();
        $children = $this->children;
        foreach ($children as $elem) {
            $this->removeChild($elem);
        }

        $this->message->format($this->getRect()->format($formated, $raw), $raw);

        if ($this->active) {
            var_dump($this);
        }
    }

    public function write(string $contents): void
    {
        $this->pulled = false;
        $this->message->format($contents, $contents);
    }

}

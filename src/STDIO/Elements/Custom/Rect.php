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

    public function pull(): string
    {
        if ($this->isClone) {
            return '';
        }

        return parent::pull();
    }

    public function onPull(): void
    {

        $formated = $this->getFormated();
        $raw = $this->getRaw();

        foreach ($this->children as $elem) {

            if ($elem instanceof self && ! $elem->isClone) {
                throw new RuntimeException('Cannot put a Rectangle inside another Rectangle.');
            }

            $this->removeChild($elem);
        }

        $this->message->format($this->getRect()->format($formated, $raw), $raw);
    }

    public function write(string $contents): void
    {
        $this->pulled = false;
        $this->message->format($contents, $contents);
    }

}

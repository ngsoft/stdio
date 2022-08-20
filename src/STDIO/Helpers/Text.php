<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

class Text extends Helper
{

    protected string $text = '';
    protected int $length = -1;
    protected Justify $justify = Justify::LEFT;
    protected Overflow $overflow = Overflow::NONE;

    public function getText(): string
    {
        return $this->text;
    }

    public function setText(string $text): static
    {
        $this->text = $text;

        return $this;
    }

    public function setJustify(Justify $justify): static
    {
        $this->justify = $justify;
        return $this;
    }

    public function setOverflow(Overflow $overflow): static
    {
        $this->overflow = $overflow;
        return $this;
    }

}

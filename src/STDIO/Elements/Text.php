<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

use Countable;
use NGSOFT\STDIO\{
    Styles\Style, Utils\Utils
};
use Stringable;
use function mb_strlen;

class Text implements Stringable, Countable
{

    protected string $text = '';
    protected ?Style $style = null;
    protected ?string $formated = null;

    public function __construct(
            string $text = '',
            ?Style $style = null
    )
    {
        $this->text = $text;
        $this->style = $style;
    }

    protected function reset(): void
    {
        $this->style = $this->formated = null;
    }

    public function getStyle(): Style
    {
        static $empty;
        $empty ??= new Style();
        return $this->style ??= $empty;
    }

    public function getFormated(): ?string
    {
        return $this->formated ??= $this->getStyle()->format($this->text);
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function setStyle(Style $style): void
    {
        $this->reset();
        $this->style = $style;
    }

    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function count(): int
    {
        return mb_strlen($this->text);
    }

    public function __toString(): string
    {
        return $this->getFormated();
    }

}

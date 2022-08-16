<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

class Message implements \Stringable, \Countable
{

    public string $formated = '';
    public string $text = '';

    public function getFormated(): string
    {
        return $this->formated;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function format(string $formated, string $text)
    {
        $this->formated = $formated;
        $this->text = $text;
    }

    public function isEmpty(): bool
    {
        return empty($this->formated);
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

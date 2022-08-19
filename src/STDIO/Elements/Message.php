<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

use Countable,
    NGSOFT\STDIO\Utils\Utils,
    Stringable;
use function mb_strlen;

class Message implements Stringable, Countable
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

    public function format(string $formated, ?string $text = null)
    {
        $this->formated = $formated;
        $this->text = $text ?? Utils::removeStyling($formated);
    }

    public function isEmpty(): bool
    {
        return empty($this->formated);
    }

    public function count(): int
    {
        return mb_strlen($this->text);
    }

    public function clear(): void
    {
        $this->formated = $this->text = '';
    }

    public function __toString(): string
    {
        return $this->getFormated();
    }

}

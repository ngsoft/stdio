<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

use IteratorAggregate;
use NGSOFT\{
    DataStructure\Tuple, STDIO\Enums\Ansi, STDIO\Styles\Style
};
use Traversable;
use function mb_strlen,
             str_starts_with;

class Segment extends Tuple implements IteratorAggregate
{

    protected string $text;
    protected ?Style $style = null;
    protected bool $isControl;

    public function __construct(string $text = '', ?Style $style = null)
    {
        $this->text = $text;
        $this->style = $style;
        $this->isControl = str_starts_with($text, Ansi::ESC);
    }

    public function getLength(): int
    {
        return $this->isControl ? 0 : mb_strlen($this->text);
    }

    public function isControl(): bool
    {
        return $this->isControl;
    }

    public function getIterator(): Traversable
    {
        yield $this->style?->format($this->text) ?? $this->text;
    }

}

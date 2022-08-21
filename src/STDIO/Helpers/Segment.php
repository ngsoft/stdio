<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

use Countable,
    IteratorAggregate;
use NGSOFT\{
    DataStructure\StringableCollection, DataStructure\Tuple, STDIO\Enums\Ansi, STDIO\Styles\Style, Traits\CloneWith
};
use Stringable,
    Traversable;
use function mb_strlen,
             str_contains,
             str_starts_with;

class Segment extends Tuple implements IteratorAggregate, Countable, Stringable
{

    use CloneWith;

    protected string $text;
    protected ?Style $style = null;
    protected bool $isControl;

    public static function create(string $text = '', ?Style $style = null)
    {
        return new static($text, $style);
    }

    public static function splitLines(self ...$segments): iterable
    {
        $line = new StringableCollection();
        foreach ($segments as $segment) {

            if ( ! $segment->isControl && str_contains($segment->text, "\n")) {
                [$text, $style] = $segment;

                $list = explode("\n", $text);

                for ($i = 0; $i < count($list); $i ++) {

                    $line->append(new static($list[$i], $style));

                    if (isset($list[$i + 1])) {
                        yield from $line;
                        $line->clear();
                    }
                }
            }

            $line->append($segment);
        }

        if ( ! $line->isEmpty()) {
            yield from $line;
        }
    }

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

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function count(): int
    {
        return $this->getLength();
    }

    public function append(string|self $text): self
    {
        $this->text .= is_string($text) ? $text : $text->text;
        return $this;
    }

    public function repeat(int $times = 1): Traversable
    {
        $times = max(1, $times);

        for ($i = 0; $i <= $times; $i ++ ) {
            yield from $this;
        }
    }

    public function getIterator(): Traversable
    {
        yield (string) $this;
    }

    public function __toString(): string
    {

        if ($this->isControl) {
            return $this->text;
        }

        return $this->style?->format($this->text) ?? $this->text;
    }

}

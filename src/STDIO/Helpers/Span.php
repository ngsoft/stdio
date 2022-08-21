<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

use InvalidArgumentException;
use NGSOFT\{
    DataStructure\Tuple, Traits\CloneWith
};

class Span extends Tuple
{

    use CloneWith;

    public function __construct(
            protected int $start,
            protected int $end
    )
    {

        if ($start >= $end) {
            throw new InvalidArgumentException(sprintf('$start cannot be greater than $end, %d > %d', $start, $end));
        }
    }

    public function getStart(): int
    {
        return $this->start;
    }

    public function getEnd(): int
    {
        return $this->end;
    }

    /**
     * Split span into two using offset
     * @param int $offset
     * @return array<int, static|null>
     */
    public function split(int $offset): array
    {


        if ($offset < $this->start || $offset >= $this->end) {
            return [$this, null];
        }

        return [
            $this->cloneWith([$this->start, $offset]),
            $this->cloneWith([$offset, $this->end]),
        ];
    }

    /**
     * Move start and end with given offset
     *
     * @param int $offset
     * @return static
     */
    public function move(int $offset): static
    {
        $offset = max(0, $offset);
        return $this->cloneWith([$this->start + $offset, $this->end + $offset]);
    }

    /**
     * Crop the span at a given offset
     *
     * @param int $offset
     * @return static
     */
    public function crop(int $offset): static
    {

        if ($offset >= $this->end) {
            return $this;
        }

        return $this->cloneWith([$this->start, $offset]);
    }

}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use InvalidArgumentException,
    NGSOFT\STDIO\Formatters\Tags\NoTag;

class TagStack
{

    /** @var Tag[] */
    protected array $stack = [];
    protected Tag $defaultTag;

    public function __construct()
    {
        $this->defaultTag = new NoTag();
    }

    public function getDefaultTag(): Tag
    {
        return $this->defaultTag;
    }

    public function setDefaultTag(Tag $defaultTag): void
    {
        $this->defaultTag = $defaultTag;
    }

    /**
     * Empty the stack
     */
    public function clear(): void
    {
        $this->stack = [];
    }

    /**
     * Adds a Tag to the stack
     */
    public function push(Tag $tag)
    {
        $this->stack[] = $tag;
    }

    /**
     * Removes last Tag frm the stack and returns it
     */
    public function pop(?Tag $tag = null): Tag
    {

        if (empty($this->stack)) {
            return $this->getDefaultTag();
        }

        if ( ! $tag) {
            return array_pop($this->stack);
        }

        foreach (array_reverse($this->stack) as $index => $current) {
            if ($current->format('') === $tag->format('')) {
                $this->stack = array_slice($this->stack, 0, $index);
                return $current;
            }
        }

        throw new InvalidArgumentException(sprintf('Incorrect closing tag "</%s>" found.', $tag->getStyle()));
    }

    /**
     * Get Current Tag
     */
    public function current(): Tag
    {
        if (empty($this->stack)) {
            return $this->getDefaultTag();
        }
        return $this->stack[count($this->stack) - 1];
    }

}

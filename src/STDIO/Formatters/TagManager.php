<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\{
    DataStructure\PrioritySet, STDIO\Styles\Styles
};

class TagManager
{

    protected PrioritySet $tags;

    public function __construct(
            protected ?Styles $styles = null
    )
    {
        $this->styles ??= new Styles();
        $this->tags = new PrioritySet();
    }

    public function register(Tag $tag): void
    {
        $class = get_class($tag);

        foreach ($this->tags as $rtag) {
            if (get_class($rtag) === $class) {
                return;
            }
        }
        $instance = clone $tag;
        $tag->setStyles($this->styles);
        $this->tags->add($instance, $instance->getPriority());
    }

}

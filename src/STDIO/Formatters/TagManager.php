<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\STDIO\Formatters\Tags\Tag;

class TagManager
{

    protected $storage = [];

    public function clear(): void
    {
        $this->storage = [];
    }

    public function push(Tag $tag): void
    {

        $index = array_search($tag, $this->storage, true) ?: -1;

        $this->storage[] = $tag;
    }

    public function current(): Tag
    {

    }

}

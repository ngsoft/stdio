<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

abstract class TagAbstract implements \NGSOFT\STDIO\Interfaces\Tag {

    public function getName(): string {

        $class = explode('\\', get_class($this));
        return strtolower(array_pop($class));
    }

}

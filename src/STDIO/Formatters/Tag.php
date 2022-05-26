<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

abstract class Tag {

    public readonly string $tagName;

    public function __construct() {
        $class = explode(NAMESPACE_SEPARATOR, static::class);
        $this->tagName = strtoupper(array_pop($class));
    }

    public function getName(): string {
        return strtolower($this->tagName);
    }

    abstract public function format(array $params): string;
}

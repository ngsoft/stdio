<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use NGSOFT\STDIO\Styles\Styles;
use const NAMESPACE_SEPARATOR;

abstract class Tag
{

    public readonly string $tagName;

    public function __construct(
            protected Styles $styles
    )
    {
        $class = explode(NAMESPACE_SEPARATOR, static::class);
        $this->tagName = strtoupper(array_pop($class));
    }

    public function getName(): string
    {
        return strtolower($this->tagName);
    }

    abstract public function format(string $message, array $params): string;
}

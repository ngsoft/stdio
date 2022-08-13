<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters\Tags;

use NGSOFT\STDIO\Styles\{
    Style, Styles
};
use function class_basename;

abstract class Tag
{

    public readonly string $name;
    protected string $contents = '';

    public function __construct(
            protected ?Styles $styles = null
    )
    {
        $this->styles ??= new Styles();
        $this->name = strtolower(class_basename(static::class));
    }

    public function getName(): string
    {
        return $this->name;
    }

    abstract public function getFormat(array $attributes): string;

    public function getStyle(array $attributes): Style
    {
        return $this->styles->createStyleFromAttributes($attributes);
    }

    public function addContents(string $contents)
    {
        $this->contents .= $contents;
    }

    public function flushContents(): string
    {
        $contents = $this->contents;
        $this->contents = '';
        return $contents;
    }

}

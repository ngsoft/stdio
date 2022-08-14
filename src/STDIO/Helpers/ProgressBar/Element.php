<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers\ProgressBar;

abstract class Element implements \Stringable
{

    protected string $name;
    protected bool $show = true;

    public function __construct(
            protected \NGSOFT\STDIO\Styles\Styles $styles
    )
    {
        $this->name = strtolower(class_basename(static::class));
    }

    abstract protected function getValue(): string;

    public function __toString(): string
    {
        return $this->getValue();
    }

}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use Countable,
    IteratorAggregate;
use NGSOFT\{
    DataStructure\PrioritySet, STDIO\Elements\Element
};
use Stringable;

class FormatterStack implements IteratorAggregate, Countable, Stringable
{

    /** @var PrioritySet<string> */
    protected static PrioritySet $_types;

    public static function register(string|Element $elementClass): void
    {

    }

}

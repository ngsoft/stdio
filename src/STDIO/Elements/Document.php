<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

use InvalidArgumentException;
use NGSOFT\{
    STDIO, STDIO\Formatters\Tag, STDIO\Styles\StyleList
};
use function str_starts_with;

class Document
{

    protected array $elements = [];

    public function __construct(
            protected ?StyleList $styles = null
    )
    {
        $this->styles ??= STDIO::getCurrentInstance()->getStyles();
    }

    public function reset()
    {
        $this->elements = [];
    }

    public function push(Element $elem)
    {
        $this->elements[] = $elem;
    }

    public function pop(?Element $elem = null): Tag
    {

        if (empty($this->elements)) {
            return new Element('', $this->styles);
        }

        if ( ! $elem) {
            return array_pop($this->elements);
        }

        foreach (array_reverse($this->elements) as $index => $current) {
            if (str_starts_with($current->getTag(), $elem->getTag())) {
                $this->elements = array_slice($this->elements, 0, $index);
                return $current;
            }
        }

        throw new InvalidArgumentException(sprintf('Incorrect closing tag "</%s>" found.', $elem->getTag()));
    }

    public function current(): Tag
    {
        if (empty($this->elements)) {
            return new Element('', $this->styles);
        }
        return $this->elements[count($this->elements) - 1];
    }

}

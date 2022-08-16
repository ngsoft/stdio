<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

use InvalidArgumentException;
use NGSOFT\{
    STDIO, STDIO\Styles\StyleList
};
use function str_starts_with;

class Document
{

    protected array $elements = [];
    protected ?Element $root;

    public function __construct(
            protected ?StyleList $styles = null
    )
    {
        $this->styles ??= STDIO::getCurrentInstance()->getStyles();
        $this->reset();
    }

    public function reset()
    {
        $this->elements = [];
        $this->root = new Element('', $this->styles);
    }

    public function push(Element $elem)
    {

        $this->current()->appendChild($elem);

        if ( ! $elem->isStandalone()) {
            $this->elements[] = $elem;
        }
    }

    public function pop(?Element $elem = null): Element
    {

        if (empty($this->elements)) {
            return $this->root;
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

    public function current(): Element
    {
        if (empty($this->elements)) {
            return $this->root;
        }
        return $this->elements[count($this->elements) - 1];
    }

    public function write(string $contents): void
    {
        $this->current()->write($contents);
    }

    public function pullContents(): string
    {
        return '';
    }

}

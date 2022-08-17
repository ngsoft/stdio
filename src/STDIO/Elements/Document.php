<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

use InvalidArgumentException;
use NGSOFT\{
    DataStructure\PrioritySet, STDIO, STDIO\Styles\StyleList
};
use function NGSOFT\Filesystem\require_all_once,
             str_starts_with;

class Document
{

    protected static PrioritySet $types;
    protected array $elements = [];
    protected Element $root;

    public function __construct(
            protected ?StyleList $styles = null
    )
    {
        $this->styles ??= new StyleList();
        self::$types ??= new PrioritySet();
        $this->autoRegister();
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

        $elem->dispatchEvent('push');
    }

    public function pop(?Element $elem = null): Element
    {

        if (empty($this->elements)) {
            return $this->root;
        }

        if ( ! $elem) {
            return array_pop($this->elements)->dispatchEvent('pop');
        }

        foreach (array_reverse($this->elements) as $index => $current) {
            if (str_starts_with($current->getTag(), $elem->getTag())) {
                $this->elements = array_slice($this->elements, 0, $index);
                return $current->dispatchEvent('pop');
            }
        }

        throw new InvalidArgumentException(sprintf('Incorrect closing tag "</%s>" found.', $elem->getTag()));
    }

    public function current(): Element
    {
        if (empty($this->elements)) {
            return $this->root->setActive(true);
        }
        return $this->elements[count($this->elements) - 1]->setActive(true);
    }

    public function isRoot(): bool
    {
        return $this->root === $this->current();
    }

    public function createElement(string $tag): Element
    {

        $params = $this->styles->getParamsFromStyleString($tag);

        /** @var Element $class */
        foreach (self::$types as $class) {
            if ($class::managesAttributes($params)) {

                return new $class($tag, $this->styles);
            }
        }


        return new Element($tag, $this->styles);
    }

    public function write(string $contents): void
    {
        $this->current()->write($contents);
    }

    public function pullContents(): string
    {

        $current = $this->current();

        while ($current->getParent()) {
            $current = $current->getParent();
        }
        return $current->pull();
    }

    public function register(string|Element $class)
    {

        if ( ! is_string($class)) {
            $class = get_class($class);
        }

        if (is_a($class, Element::class, true)) {
            self::$types->add($class, $class::getPriority());
        }
    }

    protected function autoRegister(): void
    {

        if (self::$types->isEmpty()) {
            require_all_once(__DIR__);

            foreach (implements_class(Element::class) as $class) {
                $this->register($class);
            }
        }
    }

    public function __debugInfo(): array
    {

        return [
            'root' => $this->root,
            'elements' => $this->elements,
        ];
    }

}

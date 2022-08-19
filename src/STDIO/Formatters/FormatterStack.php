<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use Countable,
    IteratorAggregate;
use NGSOFT\{
    DataStructure\PrioritySet, STDIO\Entities\DefaultEntity, STDIO\Entities\Entity, STDIO\Events\EntityEvent, STDIO\Styles\StyleList, Traits\DispatcherAware
};
use Stringable,
    Traversable;
use function implements_class,
             NGSOFT\Filesystem\require_all_once;

class FormatterStack implements IteratorAggregate, Countable, Stringable
{

    use DispatcherAware;

    /** @var PrioritySet<string> */
    protected static ?PrioritySet $_types = null;

    /** @var Entity[] */
    protected array $stack = [];
    protected Entity $root;

    public function __construct(protected ?StyleList $styles = null)
    {
        self::autoRegister();
        $this->styles ??= new StyleList();
    }

    protected static function getTypes(): PrioritySet
    {
        return static::$_types ??= new PrioritySet();
    }

    public static function register(string|Entity $class): void
    {
        if (is_object($class)) {
            $class = get_class($class);
        }

        if (is_a($class, Entity::class, true)) {
            self::getTypes()->add($class, $class::getPriority());
        }
    }

    protected static function autoRegister()
    {

        $types = static::getTypes();

        if ($types->isEmpty()) {
            require_all_once(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Entities');
            foreach (implements_class(Entity::class) as $class) {
                static::register($class);
            }
        }
    }

    protected function dispatchEvent(EntityEvent $event): EntityEvent
    {

        return ($this->eventDispatcher?->dispatch($event) ?? $event)->onEvent();
    }

    public function reset(): void
    {
        $this->root = new DefaultEntity('', $this->styles);
        $this->stack = [];
    }

    public function current(): Entity
    {

        if (empty($this->stack)) {
            return $this->root->setActive();
        }

        return $this->stack[$this->count() - 1]->setActive();
    }

    public function isRoot(): bool
    {
        return $this->current() === $this->root;
    }

    public function push(Entity $entity): void
    {

        $this->current()->appendChild($entity);
        if ( ! $entity->isStandalone()) {
            $this->stack[] = $entity;
        }
    }

    public function count(): int
    {
        return count($this->stack);
    }

    public function getIterator(): Traversable
    {

    }

    public function __toString(): string
    {

    }

}

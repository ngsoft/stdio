<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use Countable,
    InvalidArgumentException;
use NGSOFT\{
    DataStructure\PrioritySet, STDIO\Entities\DefaultEntity, STDIO\Entities\Entity, STDIO\Events\EntityEvent, STDIO\Events\EntityPop, STDIO\Events\EntityPull,
    STDIO\Events\EntityPush, STDIO\Styles\StyleList, Traits\DispatcherAware
};
use Stringable;
use function implements_class,
             NGSOFT\Filesystem\require_all_once,
             str_starts_with;

class FormatterStack implements Countable, Stringable
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

        $this->dispatchEvent(EntityPush::create($entity));
    }

    public function pop(?Entity $entity = null)
    {

        if ($this->isEmpty()) {
            return $this->root;
        }

        if ( ! $entity) {
            $entity = end($this->stack);
        }
        /** @var Entity $current */
        foreach (array_reverse($this->stack) as $index => $current) {

            if (str_starts_with($current->getTag(), $entity->getTag())) {
                $this->stack = array_splice($this->stack, 0, $index);

                $entity = $current;
                $this->dispatchEvent(EntityPop::create($entity));
                return $entity;
            }
        }

        throw new InvalidArgumentException(sprintf('Incorrect closing tag "</%s>" found.', $entity->getTag()));
    }

    public function createEntity(string $tag): Entity
    {
        $params = $this->styles->getParamsFromStyleString($tag);

        /** @var Entity $class */
        foreach (self::$_types as $class) {
            if ($class::matches($params)) {
                return $class::create($tag, $this->styles);
            }
        }

        return DefaultEntity::create($tag, $this->styles);
    }

    public function write(string $message): void
    {
        $this->current()->write($message);
    }

    public function pull(): string
    {
        $entity = $this->root;

        $this->dispatchEvent(EntityPull::create($entity));

        $result = (string) $entity;
        $entity->clear();
        return $result;
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    public function count(): int
    {
        return count($this->stack);
    }

    public function __toString(): string
    {
        return (string) $this->root;
    }

}

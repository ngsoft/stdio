<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Values;

use Generator,
    JsonSerializable,
    LogicException,
    ReflectionClass,
    ReflectionClassConstant,
    RuntimeException,
    Stringable;

/**
 * Advanced Enums
 */
abstract class Value implements Stringable, JsonSerializable {

    private static array $_constants = [];
    private static array $_labels = [];
    private static array $_methods = [];

    ////////////////////////////   Implementation   ////////////////////////////


    protected function __construct(
            public readonly string $label,
            public readonly mixed $value
    ) {

    }

    /**
     * The Label
     *
     * @return string
     */
    public function getLabel(): string {
        return $this->label;
    }

    /**
     * The Value
     *
     * @return mixed
     */
    public function getValue(): mixed {
        return $this->value;
    }

    /**
     * Compare the input against current instance value
     *
     * @param mixed $input
     * @return bool
     */
    public function is(mixed $input): bool {
        return $this === $input or $this->value === $input or
                ($input instanceof static and
                $input::class === static::class and
                $input->value === $this->value);
    }

    ////////////////////////////   Static Methods   ////////////////////////////

    /**
     * Test if the given label exists
     *
     * @param string $label
     * @return bool
     */
    final public static function hasLabel(string $label): bool {
        return \defined("static:{$label}");
    }

    /**
     * Test if the given value exists
     *
     * @param mixed $value
     * @return bool
     */
    final public static function hasValue(mixed $value): bool {

        $constants = static::getConstants();
        return in_array($value, $constants);
    }

    final public static function getConstants(): array {
        if (!isset(self::$_constants[static::class])) {
            self::$_constants[static::class] = self::$_labels[static::class] = self::$_methods[static::class] = [];

            $declared = &self::$_constants[static::class];

            /** @var ReflectionClass $reflector */
            $reflector = new ReflectionClass(static::class);

            do {
                if ($reflector->getName() === self::class) break;
                $currentValues = [];
                /** @var ReflectionClassConstant $classConstant */
                foreach ($reflector->getReflectionConstants(ReflectionClassConstant::IS_PUBLIC) as $classConstant) {
                    $name = $classConstant->getName();
                    $value = $classConstant->getValue();

                    if (array_key_exists($name, $declared)) continue;
                    if (in_array($value, $currentValues, true)) {
                        throw new RuntimeException('Duplicate value ' . $value);
                    }
                    self::$_labels[static::class][$name] = self::$_methods[static::class] [strtolower($name)] = new static($name, $value);
                    self::$_constants[static::class][$name] = $value;
                    $currentValues[] = $value;
                }
            } while (($reflector = $reflector->getParentClass()) instanceof ReflectionClass);
        }

        return $constants[static::class];
    }

    /**
     * Iterate all values
     *
     *
     * @return \Generator<string,static>
     */
    final public static function getValues(): Generator {
        $constants = static::getConstants();
        $instances = &self::$_labels[static::class];

        foreach ($constants as $label => $value) {
            yield $label => $instances[$label];
        }
    }

    /**
     * Returns instance for specified value
     *
     * @param string|int|float $value
     * @return static
     * @throws RuntimeException
     */
    final public static function from(string|int|float $value): static {
        //build the cache
        $constants = static::getConstants();
        $key = array_search($value, $constants, true);
        if (false === $key) throw new RuntimeException('Invalid value supplied.');
        return self::$_labels[$key];
    }

    ////////////////////////////   Interfaces/Magics   ////////////////////////////

    /** {@inheritdoc} */
    public static function __callStatic(string $name, array $arguments) {
        if (count($arguments) > 0) {
            throw new InvalidArgumentException(sprintf('Too many arguments for %s::%s().', static::class, $name));
        }

        static::getConstants();
        if (!isset(self::$_methods[static::class][$name])) throw new BadMethodCallException(sprintf('Invalid Method %s::%s().', static::class, $name));
        return self::$_methods[static::class][$name];
    }

    public function jsonSerialize(): mixed {
        return [$this->__toString() => $this->value];
    }

    public function __toString(): string {
        return sprintf('%s::%s', static::class, $this->label);
    }

    public function __clone() {
        throw new LogicException('Values are not cloneable');
    }

    public function __sleep() {
        throw new LogicException('Values are not serializable');
    }

    public function __wakeup() {
        throw new LogicException('Values are not serializable');
    }

}

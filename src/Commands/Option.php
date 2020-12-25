<?php

namespace NGSOFT\Commands;

use InvalidArgumentException;

class Option {

    const TYPE_SHORT = 1;
    const TYPE_VERBOSE = 2;
    const TYPE_NAMED = 3;
    const TYPE_ANONYMOUS = 4;
    const VALID_NAME_REGEX = '/^[a-z][a-z0-9\_]+$/i';
    const VALID_SHORT_REGEX = '/^\-[a-z0-9]$/i';
    const VALID_LONG_REGEX = '/^[\-]{2}[a-z0-9][a-z0-9\-]+$/i';

    /** @var string */
    protected $name;

    /** @var string */
    protected $description;

    /** @var bool */
    protected $isBoolean = false;

    /** @var mixed|null */
    protected $default = null;

    /** @var string */
    protected $long = '';

    /** @var string */
    protected $short = '';

    /** @var callable[] */
    protected $must = [];

    /** @var array */
    protected $values = [];

    /** @var int */
    protected $type = self::TYPE_ANONYMOUS;

    ////////////////////////////   Getters/Setter   ////////////////////////////

    /**
     * Get Option Type
     * @return int
     */
    public function getType(): int {
        return $this->type;
    }

    /**
     * Set Option Type
     * @param int $type
     * @return $this
     * @throws InvalidArgumentException
     */
    public function setType(int $type) {
        if (!in_array($type, [self::TYPE_SHORT, self::TYPE_VERBOSE, self::TYPE_NAMED, self::TYPE_ANONYMOUS])) {
            throw new \InvalidArgumentException("Invalid option type $type");
        }
        $this->type = $type;
        return $this;
    }

    /**
     * Auto Set Type
     * @return static
     */
    protected function autosetType(): Option {

        $type = self::TYPE_ANONYMOUS;
        if (
                !empty($this->short)
                and!empty($this->long)
        ) $type = self::TYPE_NAMED;
        elseif (!empty($this->short)) $type = self::TYPE_SHORT;
        elseif (!empty($this->long)) $type = self::TYPE_VERBOSE;
        return $this->setType($type);
    }

    /**
     * Get checks callback list
     * @return array
     */
    public function getMustBe(): array {
        return $this->must;
    }

    /**
     * Set a check callable
     * @param callable $callback
     * @return Option
     */
    public function setMustBe(callable $callback) {
        $this->must[] = $callback;
        return $this;
    }

    /**
     * Get Option Name
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Get Option Description
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Option value is Boolean
     * @return bool
     */
    public function getIsBoolean(): bool {
        return $this->isBoolean;
    }

    /**
     * Get Default Value
     * @return mixed|null
     */
    public function getDefaultValue() {
        return $this->default;
    }

    /**
     * Get Long Argument
     * @return string
     */
    public function getLongArgument(): string {
        return $this->long;
    }

    /**
     * Get Short Argument
     * @return string
     */
    public function getShortArgument(): string {
        return $this->short;
    }

    /**
     * Get Values for Option
     * @return array
     */
    public function getValues(): array {
        return $this->values;
    }

    /**
     * Get the first value
     * @return mixed|null
     */
    public function getValue() {
        $value = null;
        if (array_key_exists(0, $this->values)) $value = $this->values[0];
        return $value;
    }

    /**
     * Set Option Name
     * @param string $name
     * @return Option
     * @throws InvalidArgumentException
     */
    public function setName(string $name) {
        if (preg_match(self::VALID_NAME_REGEX, $name) === false) {
            throw new InvalidArgumentException("Invalid option name $name");
        }
        $this->name = $name;
        return $this;
    }

    /**
     * Set Option Description
     * @param string $description
     * @return Option
     */
    public function setDescription(string $description) {
        $this->description = $description;
        return $this;
    }

    /**
     * Set Boolean flag
     * @param bool $isBoolean
     * @return Option
     */
    public function setIsBoolean(bool $isBoolean) {
        $this->isBoolean = $isBoolean;
        return $this;
    }

    /**
     * Set Default Value
     * @param mixed $default
     * @return Option
     */
    public function setDefaultValue($default) {
        $this->default = $default;
        return $this;
    }

    /**
     * Set Long Value
     * @param string $long
     * @return Option
     * @throws InvalidArgumentException
     */
    public function setLongArgument(string $long) {
        if (preg_match(self::VALID_LONG_REGEX, $long) === false) {
            throw new InvalidArgumentException("Invalid long option $long");
        }
        $this->long = $long;
        return $this->autosetType();
    }

    /**
     * Set Short Value
     * @param string $short
     * @return Option
     * @throws InvalidArgumentException
     */
    public function setShortArgument(string $short) {
        if (preg_match(self::VALID_SHORT_REGEX, $short) === false) {
            throw new InvalidArgumentException("Invalid short option $short");
        }
        $this->short = $short;
        return $this->autosetType();
    }

    ////////////////////////////   Configurator   ////////////////////////////

    /**
     * Get a Clone
     * @return Option
     */
    protected function getClone(): Option {
        $clone = clone $this;
        return $clone;
    }

    /**
     * Get a clone with declared default value
     * @param mixed $default
     * @return Option
     */
    public function withDefaultValue($default): Option {
        return $this->getClone()->setDefaultValue($default);
    }

    /**
     * Get a clone with declared name
     * @param string $name
     * @return Option
     */
    public function withName(string $name): Option {
        return $this->getClone()->setName($name);
    }

    /**
     * Get a clone with declared short argument
     * @param string $short
     * @return Option
     */
    public function withShortArgument(string $short): Option {
        return $this->getClone()->setShortArgument($short);
    }

    /**
     * Get a clone with declared long argument
     * @param string $long
     * @return Option
     */
    public function withLongArgument(string $long): Option {
        return $this->getClone()->setLongArgument($long);
    }

    /**
     * Get a clone with description as declared
     * @param string $description
     * @return Option
     */
    public function withDescription(string $description): Option {
        return $this->getClone()->setDescription($description);
    }

    /**
     * Get a clone with declared boolean flag
     * @param bool $isBoolean
     * @return Option
     */
    public function withIsBoolean(bool $isBoolean): Option {
        return $this->getClone()->setIsBoolean($isBoolean);
    }

    /**
     * Get a clone with check value as declared
     * @param callable $callback
     * @return Option
     */
    public function withMustBe(callable $callback): Option {
        return $this->getClone()->setMustBe($callback);
    }

    /**
     * Get a clone with declared type
     * @param int $type
     * @return \NGSOFT\Commands\Option
     */
    public function withType(int $type): Option {
        return $this->getClone()->setType($type);
    }

    ////////////////////////////   Magics   ////////////////////////////

    /** @param string $name */
    public function __construct(string $name) {
        $this->setName($name);
    }

}

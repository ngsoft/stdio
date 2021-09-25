<?php

declare(strict_types=1);

namespace NGSOFT\Commands;

use InvalidArgumentException,
    RuntimeException;

class Option {

    //Option types
    const TYPE_SHORT = 1;
    const TYPE_VERBOSE = 2;
    const TYPE_NAMED = 3;
    const TYPE_ANONYMOUS = 4;
    //Options validation
    const VALID_NAME_REGEX = '/^[a-z][a-z0-9\_]+$/i';
    const VALID_SHORT_REGEX = '/^\-[a-z0-9]$/i';
    const VALID_LONG_REGEX = '/^[\-]{2}[a-z0-9][a-z0-9\-]+$/i';
    //Values Types
    const VALUE_TYPE_STRING = 'string';
    const VALUE_TYPE_INT = 'integer';
    const VALUE_TYPE_FLOAT = 'double';
    const VALUE_TYPE_BOOLEAN = 'boolean';
    //properties
    const OPTION_PROPERTIES = [
        'type', 'valueType',
        'name', 'description',
        'defaultValue', 'required',
        'short', 'long'
    ];

    /** @var int */
    protected $type = self::TYPE_ANONYMOUS;

    /** @var string */
    protected $valueType = self::VALUE_TYPE_STRING;

    /** @var string */
    protected $name;

    /** @var string|null */
    protected $short;

    /** @var string|null */
    protected $long;

    /** @var string */
    protected $description = '';

    /** @var mixed|null */
    protected $defaultValue = null;

    /** @var callable|null */
    protected $validate;

    /** @var callable|null */
    protected $transform;

    /** @var bool */
    protected $required = false;

    ////////////////////////////   Initialisation   ////////////////////////////

    /**
     * @param string $name
     * @param string|null $short
     * @param string|null $long
     * @throws InvalidArgumentException
     */
    public function __construct(
            string $name,
            ?string $short = null,
            ?string $long = null
    ) {
        if (preg_match(self::VALID_NAME_REGEX, $name) > 0) {
            $this->name = $name;
        } else throw new InvalidArgumentException(sprintf('Invalid argument name="%s"', $name));

        if (is_string($short)) $this->setShort($short);
        if (is_string($long)) $this->setLong($long);
    }

    /**
     * Creates a new Option
     * @param string $name name in args
     * @param string|null $short Short Argument
     * @param string|null $long Long Argument
     * @return static
     */
    public static function create(string $name, ?string $short = null, ?string $long = null): self {
        return new static($name, $short, $long);
    }

    ////////////////////////////   Configuration   ////////////////////////////

    /**
     * Get Option Params
     * @return array
     */
    public function getParams(): array {
        $result = [];
        foreach (self::OPTION_PROPERTIES as $prop) {
            $result[$prop] = $this->{$prop};
        }
        return $result;
    }

    /**
     * Auto Set Type
     * @return static
     */
    protected function autosetType(): self {

        $type = self::TYPE_ANONYMOUS;
        if (
                !empty($this->short)
                and!empty($this->long)
        ) $type = self::TYPE_NAMED;
        elseif (is_null($this->short)) $type = self::TYPE_SHORT;
        elseif (is_null($this->long)) $type = self::TYPE_VERBOSE;
        return $this->setType($type);
    }

    /**
     * Set Short argument
     * @param string $short
     * @return static
     * @throws InvalidArgumentException
     */
    public function setShort(string $short): self {
        if (preg_match(self::VALID_SHORT_REGEX, $short) > 0) {
            $this->short = $short;
        } else throw new InvalidArgumentException(sprintf('Invalid argument short="%s".', $short));
        return $this->autosetType();
    }

    /**
     * Set Long Argument
     * @param string $long
     * @return static
     * @throws InvalidArgumentException
     */
    public function setLong(string $long): self {
        if (preg_match(self::VALID_LONG_REGEX, $long) > 0) {
            $this->long = $long;
        } else throw new InvalidArgumentException(sprintf('Invalid argument long="%s".', $long));
        $this->long = $long;
        return $this->autosetType();
    }

    /**
     * Set Option Type
     * @param int $type
     * @return static
     * @throws InvalidArgumentException
     */
    public function setType(int $type): self {
        $valid = [self::TYPE_SHORT, self::TYPE_VERBOSE, self::TYPE_NAMED, self::TYPE_ANONYMOUS];
        if (!in_array($type, $valid)) {
            throw new InvalidArgumentException('Invalid argument type');
        }

        $this->type = $type;
        return $this;
    }

    /**
     * Set Value Type
     * @param string $valueType
     * @return static
     * @throws InvalidArgumentException
     */
    public function setValueType(string $valueType): self {
        $valid = [self::VALUE_TYPE_STRING, self::VALUE_TYPE_INT, self::VALUE_TYPE_FLOAT, self::VALUE_TYPE_BOOLEAN];
        if (!in_array($valueType, $valid)) {
            throw new InvalidArgumentException(sprintf('Invalid argument valueType="%s" specified(valid: %s)', $valueType, implode(', ', $valid)));
        }
        $this->valueType = $valueType;
        return $this;
    }

    /**
     * Set Description
     * @param string $description
     * @return static
     */
    public function setDescription(string $description): self {
        $this->description = $description;
        return $this;
    }

    /**
     * Set Default Value
     * @param mixed $defaultValue
     * @return static
     */
    public function setDefaultValue($defaultValue): self {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * Argument value not null
     * @param bool $required
     * @return static
     */
    public function setRequired(bool $required = true): self {
        $this->required = $required;
        return $this;
    }

    /**
     * Set the User Defined Validation Callback
     * @param callable $callback
     * @return static
     */
    public function validateWith(callable $callback): self {
        $this->validate = $callback;
        return $this;
    }

    /**
     * Set the User Defined Transform Callback
     * @param callable $callback
     * @return static
     */
    public function transformWith(callable $callback): self {
        $this->transform = $callback;
        return $this;
    }

    ////////////////////////////   Aliases   ////////////////////////////

    /**
     * Set Option Value as Boolean
     * @return static
     */
    public function setBoolean(): self {
        return $this->setValueType(self::VALUE_TYPE_BOOLEAN);
    }

    /**
     * Set Option Value as integer
     * @return static
     */
    public function setInt(): self {
        return $this->setValueType(self::VALUE_TYPE_INT);
    }

    /**
     * Set Option Value as Float
     * @return static
     */
    public function setFloat(): self {
        return $this->setValueType(self::VALUE_TYPE_FLOAT);
    }

    ////////////////////////////   ToolBox   ////////////////////////////

    /**
     * Use user function or predefined methods to validate the argument value
     * @param string $argument
     * @return boolean
     * @throws RuntimeException
     */
    public function validateArgument(string $argument) {
        $retval = true;
        if (is_callable($this->validate)) {
            $retval = call_user_func_array($this->validate, [$argument]);
            if (!is_bool($retval)) {
                throw new RuntimeException(sprintf('Invalid return value for validation callback in Option "%s", boolean requested but %s given', $this->name, gettype($retval)));
            }
            if (false === $retval) return false;
        } elseif (
                ($this->valueType == self::VALUE_TYPE_INT)
                or ($this->valueType == self::VALUE_TYPE_FLOAT)
        ) {
            $retval = is_numeric($argument);
        } elseif ($this->valueType == self::VALUE_TYPE_BOOLEAN) $retval = false;
        return $retval;
    }

    /**
     * Use user function or predefined methods to transform the argument value
     * @param string $argument
     * @return mixed
     */
    public function transformArgument(string $argument) {
        $result = $argument;
        if (is_callable($this->transform)) {
            $result = call_user_func_array($this->transform, [$argument]);
        } elseif ($this->valueType == self::VALUE_TYPE_INT) $result = intval($argument);
        elseif ($this->valueType == self::VALUE_TYPE_FLOAT) $result = floatval($argument);
        return $result;
    }

    ////////////////////////////   Getters   ////////////////////////////

    /**
     * Get Option Type
     * @return int
     */
    public function getType(): int {
        return $this->type;
    }

    /**
     * Get Option Value Type
     * @return string
     */
    public function getValueType(): string {
        return $this->valueType;
    }

    /**
     * Get Option Name
     * @return string
     */
    public function getName(): string {
        return $this->name;
    }

    /**
     * Get Description
     * @return string
     */
    public function getDescription(): string {
        return $this->description;
    }

    /**
     * Get Default Value
     * @return mixed
     */
    public function getDefaultValue() {
        return $this->defaultValue;
    }

}

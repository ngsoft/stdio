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
    private $name;

    /** @var string */
    private $description;

    /** @var bool */
    private $isBoolean = false;

    /** @var mixed|null */
    private $default = null;

    /** @var string */
    private $long = '';

    /** @var string */
    private $short = '';

    /** @var callable[] */
    private $must = [];

    /** @var array */
    private $values = [];

    ////////////////////////////   Getters/Setter   ////////////////////////////

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
        return $this;
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
        return $this;
    }

    ////////////////////////////   Configuration   ////////////////////////////

    /**
     * Get a Clone
     * @return Option
     */
    private function getClone(): Option {
        $clone = clone $this;
        return $clone;
    }

    /**
     * Get a clone with default value as declared
     * @param mixed $default
     * @return Option
     */
    public function withDefaultValue($default): Option {
        return $this->getClone()->setDefaultValue($default);
    }

    /**
     * Get a clone with name as declared
     * @param string $name
     * @return Option
     */
    public function withName(string $name): Option {
        return $this->getClone()->setName($name);
    }

    /**
     * Get a clone with short argument as declared
     * @param string $short
     * @return Option
     */
    public function withShortArgument(string $short): Option {
        return $this->getClone()->setShortArgument($short);
    }

    /**
     * Get a clone with long argument as declared
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
     * Get a clone with boolean flag as declared
     * @param bool $isBoolean
     * @return Option
     */
    public function withIsBoolean(bool $isBoolean): Option {
        return $this->getClone()->setIsBoolean($isBoolean);
    }

////////////////////////////   Magics   ////////////////////////////
}

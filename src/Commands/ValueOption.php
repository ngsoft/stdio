<?php

declare(strict_types=1);

namespace NGSOFT\Commands;

/**
 * Anonymous Option
 */
class ValueOption extends Option {

    public function __construct(string $name, $defaultValue = null) {
        parent::__construct($name);
        $this->setType(self::TYPE_ANONYMOUS);
        $this->setDefaultValue($defaultValue);
        if (is_int($defaultValue)) $this->setValueType(self::VALUE_TYPE_INT);
        elseif (is_float($defaultValue)) $this->setValueType(self::VALUE_TYPE_FLOAT);
    }

}

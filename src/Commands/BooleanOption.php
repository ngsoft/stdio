<?php

namespace NGSOFT\Commands;

class BooleanOption extends Option {

    public function __construct(string $name, string $description) {
        parent::__construct($name, $description);
        $this->isBoolean();
    }

}

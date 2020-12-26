<?php

namespace NGSOFT\Commands;

class BooleanOption extends Option {

    public function __construct(string $name, mixed $short = null, mixed $long = null) {
        parent::__construct($name, $short, $long);
        $this->isBoolean();
    }

}

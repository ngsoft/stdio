<?php

namespace NGSOFT\Commands;

class BooleanOption extends Option {

    public function __construct(string $name, string $short, ?string $long = null) {
        parent::__construct($name, $short, $long);
        $this->isBoolean();
    }

}

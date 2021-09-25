<?php

declare(strict_types=1);

namespace NGSOFT\Commands;

class BooleanOption extends Option {

    public function __construct(string $name, ?string $short = null, ?string $long = null) {
        parent::__construct($name, $short, $long);
        $this->setBoolean();
    }

}

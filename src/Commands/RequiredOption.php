<?php

namespace NGSOFT\Commands;

class RequiredOption extends Option {

    public function __construct(string $name, string $description) {
        parent::__construct($name, $description);
        $this->isRequired();
    }

}

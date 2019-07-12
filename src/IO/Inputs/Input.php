<?php

declare(strict_types=1);

namespace NGSOFT\Tools\IO\Inputs;

use NGSOFT\Tools\Interfaces\InputInterface;

abstract class Input implements InputInterface {

    /** {@inheritdoc} */
    public function readln(): string {
        return implode("", $this->read());
    }

}

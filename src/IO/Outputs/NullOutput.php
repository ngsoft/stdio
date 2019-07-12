<?php

declare(strict_types=1);

namespace NGSOFT\Tools\IO\Outputs;

class NullOutput extends Output {

    protected function doWrite(string $message, bool $newline) {

    }

}

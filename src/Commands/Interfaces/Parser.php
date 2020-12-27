<?php

declare(strict_types=1);

namespace NGSOFT\Commands\Interfaces;

use NGSOFT\Commands\Option;

interface Parser {

    /**
     * Parse Arguments and returns key value pair to use with command
     * @param array $args
     * @param Option[] $options
     * @return array<string,mixed>
     */
    public function parseArguments(array $args, array $options): array;
}

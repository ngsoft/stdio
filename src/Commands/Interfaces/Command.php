<?php

namespace NGSOFT\Commands\Interfaces;

use NGSOFT\Commands\Option;

interface Command {

    /**
     * Command to Execute
     * @param array $args
     */
    public function command(array $args);

    /**
     * Get Option List
     * @return Option[]
     */
    public function getOptions(): array;

    /**
     * Parse Arguments and returns key value pair to use with command
     * @param array $args
     * @return array
     */
    public function parseArguments(array $args): array;
}

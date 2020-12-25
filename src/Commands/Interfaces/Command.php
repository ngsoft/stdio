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
}

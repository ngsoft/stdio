<?php

namespace NGSOFT\Commands\Interfaces;

interface Parser {

    /**
     * Parse Arguments and returns key value pair to use with command
     * @param array $args
     * @return array
     */
    public function parseArguments(array $args): array;
}

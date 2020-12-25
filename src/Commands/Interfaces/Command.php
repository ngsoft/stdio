<?php

namespace NGSOFT\Commands\Interfaces;

use NGSOFT\Commands\Option;

interface Command {

    const VALID_COMMAND_NAME_REGEX = '/^[a-z][a-z0-9\_\:]+$/i';

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

    /**
     * Get Command Name
     * @return string
     */
    public function getName(): string;

    /**
     * Get Command Description
     * @return string
     */
    public function getDescription(): string;
}

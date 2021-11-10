<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Interfaces;

/**
 * Self closing Format Tag
 */
interface Tag {

    /**
     * A lowercased Tag name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * @param array<string,mixed> $params
     * @return string Formatted output
     */
    public function format(array $params): string;
}

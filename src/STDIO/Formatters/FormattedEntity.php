<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Formatters;

use Stringable;

/**
 * The entities used by the tag formatter
 */
interface FormattedEntity extends Stringable
{

    /**
     * The priority in the stack [1-INF]
     * a highest number gets executed first
     */
    public static function getPriority(): int;

    /**
     * Checks attributes to define if that entity gets executed
     * (only the first match is executed, it's why priority must be set)
     */
    public static function matches(array $attributes): bool;

    /**
     * Write the message into the entity
     */
    public function write(string $message): void;
}

<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Entities;

/**
 * @phan-file-suppress PhanUnusedPublicNoOverrideMethodParameter
 */
final class DefaultEntity extends Entity
{

    public static function getPriority(): int
    {
        return 1;
    }

    public static function matches(array $attributes): bool
    {
        return true;
    }

}

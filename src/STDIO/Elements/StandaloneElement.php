<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Elements;

class StandaloneElement extends Element
{

    protected bool $isStandalone = true;

    public static function getPriority(): int
    {
        return 10;
    }

    public static function managesAttributes(array $attributes): bool
    {

        static $managed = ['br', 'hr', 'tab'];

        return count($managed) !== array_diff($managed, array_keys($attributes));
    }

    public function write(string $contents): void
    {

    }

}

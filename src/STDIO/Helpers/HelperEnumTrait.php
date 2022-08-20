<?php

declare(strict_types=1);

namespace NGSOFT\STDIO\Helpers;

use NGSOFT\Enums\EnumTrait;

/**
 * @phan-file-suppress PhanTypeMismatchReturn,PhanTypeMismatchDeclaredParam, PhanUndeclaredProperty
 */
trait HelperEnumTrait
{

    use EnumTrait;

    public function getParam(): string
    {
        return strtolower(class_basename($this));
    }

    public function getParamValue(): string
    {
        return strtolower($this->getName());
    }

    public static function DEFAULT(): static
    {
        return static::cases()[0];
    }

}

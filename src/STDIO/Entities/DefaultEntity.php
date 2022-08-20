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

    public function onPull()
    {
        if ($this->isActive()) {

            foreach ($this->getChildrenEntities() as $entity) {
                if (method_exists($entity, __FUNCTION__)) {
                    call_user_func([$entity, __FUNCTION__]);
                }
            }
        }
    }

}

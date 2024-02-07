<?php

namespace App\Core\Utils;

use App\Model\Trait\SoftDeleteTrait;

class Model
{

    public static function isSoftDeletable($entity): bool
    {
        $reflection = new \ReflectionClass($entity);
        foreach ($reflection->getTraits() as $trait => $reflectionTrait) {
            if ($trait == SoftDeleteTrait::class) {
                return true;
            }
        }

        return false;
    }
}

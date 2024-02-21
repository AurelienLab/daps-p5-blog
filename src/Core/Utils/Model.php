<?php

namespace App\Core\Utils;

use App\Model\Trait\SoftDeleteTrait;

class Model
{

    /**
     * Analyse Model class to check if it's a soft deletable model
     *
     * @param $entity
     *
     * @return bool
     * @throws \ReflectionException
     */
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

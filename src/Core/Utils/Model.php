<?php

namespace App\Core\Utils;

use App\Model\Trait\SoftDeleteTrait;
use App\Model\Trait\TimestampableTrait;

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
        foreach (array_keys($reflection->getTraits()) as $trait) {
            if ($trait == SoftDeleteTrait::class) {
                return true;
            }
        }

        return false;
    }

    /**
     * Analyse Model class to check if it's a timestampable model
     *
     * @param $entity
     *
     * @return bool
     * @throws \ReflectionException
     */
    public static function isTimestampable($entity): bool
    {
        $reflection = new \ReflectionClass($entity);
        foreach (array_keys($reflection->getTraits()) as $trait) {
            if ($trait == TimestampableTrait::class) {
                return true;
            }
        }

        return false;
    }
}

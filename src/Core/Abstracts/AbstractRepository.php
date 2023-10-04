<?php

namespace App\Core\Abstracts;

use App\Core\Database\Database;
use App\Core\Database\Query;
use PDO;
use ReflectionClass;

abstract class AbstractRepository
{

    const MODEL = '';


    /**
     * Get all records for entity managed by current repository
     *
     * @return false|array
     * @throws \Exception
     */
    public static function getAll(): false|array
    {
        $query = new Query(static::MODEL);
        $query->select();

        return Database::query($query);
    }


    /**
     * Update or Create entity in database
     *
     * @param $entity
     *
     * @return void
     * @throws \Exception
     */
    public static function save($entity): void
    {
        $dbMapping = Database::mapEntityToTable($entity, static::MODEL);
        $query = new Query(static::MODEL);

        if (isset($dbMapping->entityArray[$dbMapping->primaryKey]) === false) {
            $query->insert($dbMapping->entityArray);
        } else {
            $query->update($dbMapping->entityArray, $dbMapping->primaryKey);
        }

        Database::query($query);
    }
}

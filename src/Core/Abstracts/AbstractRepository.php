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
    public static function save($entity)
    {
        $query = new Query(static::MODEL);
        $query->describe();

        $reflectionClass = new ReflectionClass(get_class($entity));
        $entityArray = array();
        foreach ($reflectionClass->getProperties() as $property) {
            $property->setAccessible(true);
            $entityArray[$property->getName()] = $property->getValue($entity);
            $property->setAccessible(false);
        }

        $tableArray = Database::query($query, true, PDO::FETCH_COLUMN);
        // dd($entityArray, array_values($tableArray));
        foreach (array_diff(array_keys($entityArray), $tableArray) as $keyToRemove) {
            unset($entityArray[$keyToRemove]);
        }

        dd($entityArray);

        $query->insert($entity);
    }
}

<?php

namespace App\Core\Abstracts;

use App\Core\Database\Database;
use App\Core\Database\Query;
use App\Core\Exception\NotFoundException;
use Exception;
use stdClass;

abstract class AbstractRepository
{

    const MODEL = '';


    /**
     * Get all records for entity managed by current repository
     *
     * @return false|array
     * @throws Exception
     */
    public static function getAll(): false|array
    {
        $query = new Query(static::MODEL);
        $query->select();

        return Database::query($query);
    }


    /**
     * Get an entity by its primary key value
     *
     * @param mixed $identifier value of primary key of requested entity
     *
     * @return mixed
     * @throws Exception
     */
    public static function get(mixed $identifier): mixed
    {
        $primaryKey = Database::getPrimaryKey(static::MODEL);

        $query = new Query(static::MODEL);
        $query
            ->select()
            ->where($primaryKey, '=', $identifier);

        $result = Database::query($query);

        $resultCount = count($result);
        if ($resultCount > 1) {
            throw new Exception(sprintf('Too many results, expected 1 got %s', $resultCount));
        }

        if ($resultCount === 0) {
            return false;
        }

        return $result[0];
    }

    public static function getOrError(mixed $identifier): mixed
    {
        $result = self::get($identifier);

        if (!$result) {
            throw new NotFoundException();
        }

        return $result;
    }


    /**
     * Update or Create entity in database
     *
     * @param Object $entity Entity to save
     *
     * @return void
     * @throws Exception
     */
    public static function save(object $entity): void
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

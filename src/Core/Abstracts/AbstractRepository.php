<?php

namespace App\Core\Abstracts;

use App\Core\Database\Database;
use App\Core\Database\Query;
use Exception;

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
     * @param $identifier
     *
     * @return mixed
     * @throws Exception
     */
    public static function get($identifier): mixed
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

    /**
     * Update or Create entity in database
     *
     * @param $entity
     *
     * @return void
     * @throws Exception
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

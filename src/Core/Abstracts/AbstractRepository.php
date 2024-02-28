<?php

namespace App\Core\Abstracts;

use App\Core\Database\Database;
use App\Core\Database\Query;
use App\Core\Exception\NotFoundException;
use App\Core\Utils\Model;
use App\Core\Utils\Str;
use App\Model\Trait\SoftDeleteTrait;
use Exception;
use stdClass;

abstract class AbstractRepository
{

    const MODEL = '';
    const DEFAULT_RELATIONS = [];


    /**
     * Get all records for entity managed by current repository
     *
     * @param array $relations
     *
     * @return false|array
     * @throws Exception
     */
    public static function getAll($relations = []): false|array
    {
        if (empty($relations)) {
            $relations = static::DEFAULT_RELATIONS;
        }

        $query = new Query(static::MODEL);
        $query->select();

        self::addRelationsToQuery($relations, $query);

        return Database::query($query);
    }


    /**
     * Get an entity by its primary key value
     *
     * @param mixed $identifier value of primary key of requested entity
     * @param array $relations
     *
     * @return mixed
     * @throws Exception
     */
    public static function get(mixed $identifier, $relations = []): mixed
    {
        if (empty($relations)) {
            $relations = static::DEFAULT_RELATIONS;
        }

        $primaryKey = Database::getPrimaryKey(static::MODEL);

        $query = new Query(static::MODEL);
        $query
            ->select()
            ->where(static::MODEL::TABLE.'.'.$primaryKey, '=', $identifier);

        self::addRelationsToQuery($relations, $query);

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
     * Same as get() but throw a NotFoundException if no result
     *
     * @param mixed $identifier
     * @param array $relations
     *
     * @return mixed
     * @throws NotFoundException
     */
    public static function getOrError(mixed $identifier, $relations = []): mixed
    {
        if (empty($relations)) {
            $relations = static::DEFAULT_RELATIONS;
        }

        $result = self::get($identifier, $relations);

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
    public static function save(object $entity): object
    {
        $dbMapping = Database::mapEntityToTable($entity, static::MODEL);
        $query = new Query(static::MODEL);

        if (isset($dbMapping->entityArray[$dbMapping->primaryKey]) === false) {
            $query->insert($dbMapping->entityArray);
        } else {
            $query->updateOne($dbMapping->entityArray, $dbMapping->primaryKey);
        }

        $result = Database::query($query);

        if (isset($dbMapping->entityArray[$dbMapping->primaryKey]) === false) {
            $entity = static::get($result);
        } else {
            static::refresh($entity);
        }

        return $entity;
    }


    /**
     * Remove an entity from the database
     *
     * @param object $entity
     *
     * @return void
     * @throws Exception
     */
    public static function remove(object $entity, $hard = false): void
    {
        $softDelete = false;
        if ($hard == false) {
            if (Model::isSoftDeletable($entity)) {
                $entity->setDeletedAt(new \DateTime());
                $softDelete = true;
            }
        }

        $dbMapping = Database::mapEntityToTable($entity, static::MODEL);
        $query = new Query(static::MODEL);

        if ($softDelete === true) {
            $query->updateOne($dbMapping->entityArray, $dbMapping->primaryKey);
        } else {
            $query->deleteOne($dbMapping->primaryKey, $dbMapping->entityArray[$dbMapping->primaryKey]);
        }

        Database::query($query);
    }


    public static function refresh(object $entity): void
    {
        $dbMapping = Database::mapEntityToTable($entity, static::MODEL);

        if (isset($dbMapping->entityArray[$dbMapping->primaryKey]) === true) {
            $entity = static::get($dbMapping->entityArray[$dbMapping->primaryKey]);
        }
    }


    /**
     * Transforms array of relation names to left join in query
     *
     * @param array $relations
     * @param Query $query
     *
     * @return void
     * @throws Exception
     */
    protected static function addRelationsToQuery(array $relations, Query $query)
    {
        if (empty($relations)) {
            $relations = static::DEFAULT_RELATIONS;
        }

        if (!empty($relations)) {
            $reflection = new \ReflectionClass(static::MODEL);
            foreach ($relations as $relation) {
                if ($reflection->hasProperty($relation)) {
                    $setter = 'set'.Str::toPascalCase($relation);
                    if ($reflection->hasMethod($setter)) {
                        $class = $reflection->getMethod($setter)->getParameters()[0]->getType()->getName();
                        $query->leftJoin($class, [static::MODEL::TABLE.'.'.$relation.'_id', $class::TABLE.'.id']);
                    }
                }
            }
        }
    }


}

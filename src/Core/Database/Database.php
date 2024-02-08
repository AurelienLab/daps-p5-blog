<?php

namespace App\Core\Database;

use App\Core\Utils\Str;
use App\Model\Comment;
use App\Model\Trait\TimestampableTrait;
use Exception;
use PDO;
use ReflectionClass;
use stdClass;

/**
 *
 */
class Database
{

    /**
     * @var string
     */
    private string $dbName;

    /**
     * @var string
     */
    private string $dbUser;

    /**
     * @var string
     */
    private string $dbPassword;

    /**
     * @var string
     */
    private string $dbHost;

    /**
     * @var int
     */
    private int $dbPort;

    private array $tableData = [];

    private int $queryCount = 0;

    /**
     * @var PDO
     */
    private PDO $connexion;

    private static $_instance;


    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->dbName = config('db.db_name');
        $this->dbUser = config('db.db_user');
        $this->dbPassword = config('db.db_password');
        $this->dbHost = config('db.db_host');
        $this->dbPort = config('db.db_port');

        $connexionString = 'mysql:host='.$this->dbHost.':'.$this->dbPort.';dbname='.$this->dbName;

        $this->connexion = new PDO($connexionString, $this->dbUser, $this->dbPassword);
    }


    /**
     * @return mixed
     * @throws Exception
     */
    private static function getPDOInstance(): PDO
    {
        if (self::$_instance === null) {
            self::$_instance = new Database();
        }

        return self::$_instance->connexion;
    }


    /**
     * Execute the statement generated with a Query object, and map the fetched
     * data to the model passed in Query object constructor
     *
     * @param Query $query generated Query object
     * @param bool $raw Map to model (false) or not (true)
     * @param int $fetchFlag Fetch const from PDO
     *
     * @return mixed
     * @throws Exception
     */
    public static function query(
        Query $query,
        bool  $raw = false,
        int   $fetchFlag = PDO::FETCH_ASSOC,
        bool  $withSubquery = true
    ): mixed
    {
        $database = self::getPDOInstance();
        self::$_instance->queryCount++;
        $statement = $query->getStatement();

        $sth = $database->prepare($statement);

        foreach ($query->getParameters() as $key => $parameter) {
            $bindType = PDO::PARAM_STR;
            if (is_numeric($parameter) === true) {
                $bindType = PDO::PARAM_INT;
            }

            if (is_bool($parameter) === true) {
                $bindType = PDO::PARAM_BOOL;
            }

            $sth->bindValue($key, $parameter, $bindType);
        }

        try {
            $sth->execute();
        } catch (Exception $e) {
            dd($e, $statement);
        }


        if ($query->isInsert() === true) {
            return $database->lastInsertId();
        }

        if ($raw === true) {
            return $sth->fetchAll($fetchFlag);
        }


        $result = [];

        $queryResult = $sth->fetchAll();

        if ($query->getFirstOrLast() !== null) {
            if (empty($queryResult)) {
                return null;
            }

            if ($query->getFirstOrLast() === Query::GET_FIRST) {
                $result = self::mapToModel($queryResult[0], $query->getModel(), $query, $withSubquery);
                if ($withSubquery) {
                    self::queryRelations($result);
                }
                return $result;
            }

            if ($query->getFirstOrLast() === Query::GET_LAST) {
                $result = self::mapToModel(end($queryResult), $query->getModel(), $query, $withSubquery);
                if ($withSubquery) {
                    self::queryRelations($result);
                }
                return $result;
            }
        }

        foreach ($queryResult as $data) {
            $result[] = self::mapToModel($data, $query->getModel(), $query, $withSubquery);
        }

        if ($withSubquery) {
            self::queryRelations($result);
        }

        return $result;
    }


    /**
     * Use data from DB to generate a new instance of the model passed in Query
     *
     * @param array $originalData Data from database
     * @param string $model Class of the target entity model
     * @param Query $query The original query object
     *
     * @return mixed
     * @throws \ReflectionException
     */
    private static function mapToModel(array $originalData, string $model, Query $query, bool $lazy = true): mixed
    {
        $joins = $query->getJoins();
        $selectors = $query->getSelect();

        $data = [];
        foreach ($selectors as $field => $alias) {
            if (isset($originalData[$alias]) === true) {
                $data[$field] = $originalData[$alias];
            }
        }

        $entity = new $model();

        $reflectionClass = new ReflectionClass($model);
        $properties = $reflectionClass->getProperties();
        foreach ($properties as $property) {
            $propertyName = Str::toSnakeCase($property->getName());
            $setter = 'set'.Str::toPascalCase($property->getName());

            // Try to get data from database
            $fieldName = $reflectionClass->getConstant('TABLE').'.'.$propertyName;
            if ($reflectionClass->hasMethod($setter) === true) {
                $type = $reflectionClass->getMethod($setter)->getParameters()[0]->getType();

                if (isset($data[$fieldName]) === true || isset($data[$fieldName.'_id']) === true) {
                    $value = isset($data[$fieldName]) === true ? $data[$fieldName] : $data[$fieldName.'_id'];

                    if ($type->isBuiltin() === false && $value !== null) {
                        $name = $type->getName();
                        $subClassReflection = new ReflectionClass($name);

                        if (str_contains($subClassReflection->getNamespaceName(), 'App\Model')) {
                            $relation = null;
                            foreach ($joins as $join) {
                                if ($join['model'] === $subClassReflection->getName()) {
                                    $relation = self::mapToModel($originalData, $subClassReflection->getName(), $query);
                                }
                            }
                            if ($relation === null) {
                                if ($lazy == false) {
                                    continue;
                                }
                                $repository = 'App\Repository\\'.$subClassReflection->getShortName().'Repository';
                                $relation = $repository::get($value);
                            }

                            $value = $relation;
                        } else {
                            $value = new $name($value);
                        }
                    }

                    $entity->$setter($value);
                    continue;
                }
            }
        }
        
        return $entity;
    }

    /**
     * Get the amount of query executed by this class (for debug only)
     *
     * @return int
     */
    public static function getQueryCount(): int
    {
        if (self::$_instance !== null) {
            return self::$_instance->queryCount;
        }

        return 0;
    }


    /**
     * @param stdClass $entity Entity to convert
     * @param string $model Original model
     *
     * @return stdClass
     * @throws Exception
     */
    public static function mapEntityToTable(object $entity, string $model): stdClass
    {
        // Get table fields.
        $tableFields = self::getTableData($model);


        // Transform entity to array.
        $reflectionClass = new ReflectionClass(get_class($entity));

        //Set timestamps if applicable
        foreach ($reflectionClass->getTraits() as $trait => $reflexionTrait) {
            if ($trait == TimestampableTrait::class) {
                if (is_null($entity->getCreatedAt())) {
                    $entity->setCreatedAt(new \DateTime());
                }

                $entity->setUpdatedAt(new \DateTime());
            }
        }

        $entityArray = array();
        foreach ($reflectionClass->getProperties() as $property) {
            if ($property->isInitialized($entity) === true) {
                $propertyName = Str::toSnakeCase($property->getName());
                $property->setAccessible(true);
                if ($property->getValue($entity) instanceof \DateTimeInterface) {
                    $entityArray[$propertyName] = $property->getValue($entity)->format('Y-m-d H:i:s');
                } elseif (str_starts_with($property->getType(), 'App\Model\\')) {
                    $entityArray[$propertyName.'_id'] = $property->getValue($entity)->getId();
                } else {
                    $entityArray[$propertyName] = $property->getValue($entity);
                }
                $property->setAccessible(false);
            }
        }

        $primaryKey = '';
        $tableArray = [];

        // Get fields name & primary key.
        foreach ($tableFields as $field) {
            if ($field['Key'] === 'PRI') {
                $primaryKey = $field['Field'];
            }
            $tableArray[] = $field['Field'];
        }

        // Only get property available in table.
        foreach (array_diff(array_keys($entityArray), $tableArray) as $keyToRemove) {
            unset($entityArray[$keyToRemove]);
        }

        $result = new stdClass();

        $result->primaryKey = $primaryKey;
        $result->entityArray = $entityArray;

        return $result;
    }

    /**
     * Get information about the table associated to $model
     *
     * @param string $model Model to get table information from
     *
     * @return false|array
     * @throws Exception
     */
    private static function getTableData(string $model): false|array
    {
        if (isset(self::$_instance->tableData[$model]) === false) {
            $query = new Query($model);
            $query->describe();
            self::$_instance->tableData[$model] = self::query($query, true);
        }

        return self::$_instance->tableData[$model];
    }

    /**
     * Get a list of field names
     *
     * @param string $model
     *
     * @return false|array
     * @throws Exception
     */
    public static function getTableFields(string $model): false|array
    {
        $fields = [];
        foreach (self::getTableData($model) as $field) {
            $fields[] = $field['Field'];
        }

        return $fields;
    }


    /**
     * Return the primary key name of the table associated with $model
     *
     * @param string $model Model to get table primary key from
     *
     * @return string|null
     * @throws Exception
     */
    public static function getPrimaryKey(string $model): ?string
    {
        $tableFields = self::getTableData($model);
        foreach ($tableFields as $field) {
            if ($field['Key'] === 'PRI') {
                return $field['Field'];
            }
        }

        return null;
    }

    private static function queryRelations(mixed $result)
    {
        if (empty($result)) {
            return;
        }

        if (is_array($result) === false) {
            $result = [$result];
        }

        $reflectionClass = new ReflectionClass($result[0]);
        $primaryKey = self::getPrimaryKey($reflectionClass->getName());
        $primaryKeyGetter = 'get'.Str::toPascalCase($primaryKey);

        // Check if there is any needed relation
        $relationsToFetch = [];
        $properties = $reflectionClass->getProperties();
        foreach ($properties as $property) {
            $getter = 'get'.Str::toPascalCase($property->getName());
            if ($reflectionClass->hasMethod($getter) === true) {
                $getterResult = $result[0]->$getter();
                if ($getterResult instanceof EntityCollection) {
                    $relationsToFetch[$property->getName()] = $getterResult;
                }
            }
        }

        if (empty($relationsToFetch) === false) {
            $ids = [];
            foreach ($result as $item) {
                $ids[$item->$primaryKeyGetter()] = $item;
            }

            foreach ($relationsToFetch as $name => $relation) {
                $sourceEntityFieldName = Str::toSnakeCase($reflectionClass->getShortName()).'_id';
                if ($relation->getRelationType() === EntityCollection::TYPE_MANY_TO_MANY) {
                    $query = new Query($relation->getRelationModel());
                    $query->leftJoin(
                        $relation->getRelatedEntity(),
                        [
                            $relation->getRelationModel()::TABLE.'.'.$relation->getTargetEntityProperty().'_id',
                            $relation->getRelatedEntity()::TABLE.'.id'
                        ]
                    )
                        ->leftJoin(
                            $reflectionClass->getName(),
                            [
                                $reflectionClass->getName()::TABLE.'.id',
                                $relation->getRelationModel()::TABLE.'.'.$sourceEntityFieldName
                            ]
                        );
                } else {
                    $query = new Query($relation->getRelatedEntity());
                    $query->leftJoin(
                        $reflectionClass->getName(),
                        [
                            $reflectionClass->getName()::TABLE.'.id',
                            $relation->getRelatedEntity()::TABLE.'.'.$sourceEntityFieldName
                        ]
                    );
                }


                $query
                    ->select()
                    ->where($sourceEntityFieldName, 'IN', array_keys($ids));

                $reflection = new \ReflectionClass($relation->getRelatedEntity());
                foreach ($relation->getTargetRelations() as $targetRelation) {
                    if ($reflection->hasProperty($targetRelation)) {
                        $setter = 'set'.Str::toPascalCase($targetRelation);
                        if ($reflection->hasMethod($setter)) {
                            $class = $reflection->getMethod($setter)->getParameters()[0]->getType()->getName();
                            $query->leftJoin($class, [$relation->getRelatedEntity()::TABLE.'.'.$targetRelation.'_id', $class::TABLE.'.id']);
                        }
                    }
                }


                $results = self::query($query, false, PDO::FETCH_ASSOC, false);

                $originalGetter = 'get'.Str::toPascalCase($reflectionClass->getShortName());
                $collectionGetter = 'get'.Str::toPascalCase($name);

                if ($relation->getRelationType() === EntityCollection::TYPE_MANY_TO_MANY) {
                    $targetGetter = 'get'.Str::toPascalCase($relation->getTargetEntityProperty());
                }

                foreach ($results as $result) {
                    $originalObject = $ids[$result->$originalGetter()->$primaryKeyGetter()];
                    if ($relation->getRelationType() === EntityCollection::TYPE_MANY_TO_MANY) {
                        $target = $result->$targetGetter();
                    } else {
                        $target = $result;
                    }

                    $originalObject->$collectionGetter()->add($target);
                }
            }
        }
    }
}

<?php

namespace App\Core\Database;

use App\Core\Utils\Str;
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
     * @return array|false
     * @throws Exception
     */
    public static function query(Query $query, bool $raw = false, int $fetchFlag = PDO::FETCH_ASSOC): false|array
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

        $sth->execute();

        if ($raw === true) {
            return $sth->fetchAll($fetchFlag);
        }


        $result = [];
        foreach ($sth->fetchAll() as $data) {
            $result[] = self::mapToModel($data, $query->getModel(), $query);
        }
        return $result;
    }


    /**
     * Use data from DB to generate a new instance of the model passed in Query
     *
     * @param array $data Entity in array format
     * @param string $model Class of the target entity model
     *
     * @return mixed
     * @throws Exception
     */
    private static function mapToModel(array $originalData, string $model, Query $query): mixed
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

        foreach ($data as $key => $value) {
            if (str_starts_with($key, $model::TABLE.'.') === false) {
                continue;
            }

            $key = str_replace($model::TABLE.'.', '', $key);

            if (str_ends_with($key, '_id') === true) {
                $key = str_replace('_id', '', $key);
            }

            if ($reflectionClass->hasProperty(Str::toCamelCase($key)) === true) {
                $setter = 'set'.Str::toPascalCase($key);

                if ($reflectionClass->hasMethod($setter) === true) {
                    $type = $reflectionClass->getMethod($setter)->getParameters()[0]->getType();

                    if (!$type->isBuiltin() && $value !== null) {
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
                continue;

                throw new Exception(sprintf('Unable to find a setter for %s in %s', $key, $model));
            }
        }

        return $entity;
    }

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
}

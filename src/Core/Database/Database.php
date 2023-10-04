<?php

namespace App\Core\Database;

use App\Core\Config\Config;
use App\Core\Utils\Str;
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
    private $dbName;

    /**
     * @var string
     */
    private $dbUser;

    /**
     * @var string
     */
    private $dbPassword;

    /**
     * @var string
     */
    private $dbHost;

    /**
     * @var string
     */
    private $dbPort;

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
        if (null === self::$_instance) {
            self::$_instance = new Database();
        }
        return self::$_instance->connexion;
    }


    /**
     * Execute the statement generated with a Query object, and map the fetched
     * data to the model passed in Query object constructor
     *
     * @param Query $query
     * @param $raw
     * @param $fetchFlag
     *
     * @return array|false
     * @throws Exception
     */
    public static function query(Query $query, $raw = false, $fetchFlag = PDO::FETCH_ASSOC)
    {
        $database = self::getPDOInstance();
        $statement = $query->getStatement();
        if ($query->getWhere() !== null) {
            $statement .= $query->getWhere();
        }

        $statement .= ';';

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
            $result[] = self::mapToModel($data, $query->getModel());
        }
        return $result;
    }


    /**
     * Use data from DB to generate a new instance of the model passed in Query
     *
     * @param $data
     * @param $model
     *
     * @return mixed
     * @throws Exception
     */
    private static function mapToModel($data, $model): mixed
    {
        $entity = new $model();

        foreach ($data as $key => $value) {
            if (property_exists($entity, Str::toCamelCase($key)) === true) {
                $setter = 'set'.Str::toPascalCase($key);

                if (method_exists($entity, $setter) === true) {
                    $entity->$setter($value);
                    continue;
                }

                throw new Exception(sprintf('Unable to find a setter for %s in %s', $key, $model));
            }
        }

        return $entity;
    }

    /**
     * @param $entity
     * @param string $model
     *
     * @return stdClass
     * @throws Exception
     */
    public static function mapEntityToTable($entity, string $model): stdClass
    {
        // Get table fields
        $tableFields = self::getTableData($model);

        // Transform entity to array
        $reflectionClass = new ReflectionClass(get_class($entity));
        $entityArray = array();
        foreach ($reflectionClass->getProperties() as $property) {
            if ($property->isInitialized($entity)) {
                $property->setAccessible(true);
                $entityArray[$property->getName()] = $property->getValue($entity);
                $property->setAccessible(false);
            }
        }

        $primaryKey = '';
        $tableArray = [];

        // Get fields name & primary key
        foreach ($tableFields as $field) {
            if ($field['Key'] === 'PRI') {
                $primaryKey = $field['Field'];
            }
            $tableArray[] = $field['Field'];
        }

        // Only get property available in table
        foreach (array_diff(array_keys($entityArray), $tableArray) as $keyToRemove) {
            unset($entityArray[$keyToRemove]);
        }

        $result = new stdClass();

        $result->primaryKey = $primaryKey;
        $result->entityArray = $entityArray;

        return $result;
    }

    /**
     * @throws Exception
     */
    private static function getTableData(string $model): false|array
    {
        $query = new Query($model);
        $query->describe();
        return self::query($query, true);
    }

    /**
     * @throws Exception
     */
    public static function getPrimaryKey(string $model)
    {
        $tableFields = self::getTableData($model);
        foreach ($tableFields as $field) {
            if ($field['Key'] === 'PRI') {
                return $field['Field'];
            }
        }
    }
}

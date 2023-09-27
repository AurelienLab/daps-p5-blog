<?php

namespace App\Core\Database;

use App\Core\Config\Config;
use PDO;

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
     * @throws \Exception
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
     * @throws \Exception
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
     * @throws \Exception
     */
    public static function query(Query $query, $raw = false, $fetchFlag = PDO::FETCH_ASSOC)
    {
        $database = self::getPDOInstance();
        $statement = $query->getStatement().';';
        $parameters = $query->getParameters();

        $sth = $database->prepare($statement);

        $sth->execute($parameters);

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
     * @throws \Exception
     */
    private static function mapToModel($data, $model)
    {
        $entity = new $model();

        foreach ($data as $key => $value) {
            // TODO: propriétés en snake case ?
            if (property_exists($entity, $key) === true) {
                $setter = 'set'.str_replace('_', '', ucwords($key, '_'));

                if (method_exists($entity, $setter) === true) {
                    $entity->$setter($value);
                    continue;
                }

                throw new \Exception(sprintf('Unable to find a setter for %s in %s', $key, $model));
            }
        }

        return $entity;
    }
}

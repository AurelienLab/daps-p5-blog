<?php

namespace App\Core\Database;

use Exception;

class Query
{

    /**
     * @var string
     */
    private string $statement;

    /**
     * @var string
     */
    private string $table;

    /**
     * @var string
     */
    private string $model;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var string|null
     */
    private ?string $where = null;

    /**
     * @var int
     */
    private int $whereCount = 0;

    /**
     * @param string $model Model on which we'll do a query
     *
     * @throws Exception
     */

    public function __construct(string $model)
    {
        if (empty($model) === true) {
            throw new Exception('Model class must by defined in respository class');
        }

        if (empty($model::TABLE) === true) {
            throw new Exception(sprintf('Model table must by defined in %s', $model));
        }

        $this->statement = '';
        $this->table = $model::TABLE;
        $this->model = $model;
    }


    /**
     * Initiate a Select statement
     *
     * @param array|string $fields Database fields to fetch. Can be an array of string or a string (default = '*')
     *
     * @return $this
     */
    public function select(array|string $fields = '*'): self
    {
        $this->statement .= 'SELECT ';

        $fieldsStr = $fields;

        if (is_array($fields) === true) {
            $fieldsStr = implode(', ', $fields);
        }

        $this->statement .= $fieldsStr.' FROM '.$this->table;

        return $this;
    }


    /**
     * @param string $column Database column
     * @param string $comparator basic SQL comparator (=, <, >, >= ...)
     * @param mixed $value Value to match
     *
     * @return $this
     */
    public function where(string $column, string $comparator, mixed $value): self
    {
        $whereStatement = ' AND ';
        if ($this->whereCount === 0) {
            $whereStatement = ' WHERE ';
        }
        $parameterName = ':'.$column.'_'.$this->whereCount;

        $whereStatement .= implode(' ', [$column, $comparator, $parameterName]);

        $this->where .= $whereStatement;
        $this->parameters[$parameterName] = $value;

        $this->whereCount++;
        return $this;
    }

    /**
     * Generate an INSERT statement from object instance data
     *
     * @param array $data Entity in array format
     *
     * @return void
     */
    public function insert(array $data): void
    {
        $this->setParameters($data);

        $this->statement = 'INSERT INTO '.$this->table;

        $columns = ' ('.implode(', ', array_keys($data)).') ';
        $values = 'VALUES('.implode(', ', array_keys($this->parameters)).')';

        $this->statement .= $columns.$values;
    }

    /**
     * Generate an UPDATE statement from object instance data
     *
     * @param array $data entity in array format
     * @param string $primaryKey name of the primary key
     *
     * @return void
     */
    public function update(array $data, string $primaryKey): void
    {
        $this->setParameters($data);
        unset($data[$primaryKey]);

        $this->statement = 'UPDATE '.$this->table.' SET ';

        $columns = array_keys($data);

        for ($i = 0; $i < count($data); $i++) {
            if ($i > 0) {
                $this->statement .= ', ';
            }

            $this->statement .= $columns[$i].'= :'.$columns[$i];
        }

        $this->statement .= ' WHERE '.$primaryKey.' = :'.$primaryKey;
    }


    /**
     * Describe table statement (get columns information)
     *
     * @return void
     */
    public function describe(): void
    {
        $this->statement = 'DESCRIBE '.$this->table;
    }


    /**
     * @return string
     */
    public function getStatement(): string
    {
        return $this->statement;
    }


    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Generate parameters array with ":parameter" notation as key
     *
     * @param array $data Transform array of parameters to PDO compliant parameters array
     *
     * @return void
     */
    private function setParameters(array $data): void
    {
        $paramsNames = array_map(function ($element) {
            return ':'.$element;
        }, array_keys($data));
        $paramsValues = array_values($data);

        $this->parameters = array_combine($paramsNames, $paramsValues);
    }

    /**
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getWhere(): ?string
    {
        return $this->where;
    }
}

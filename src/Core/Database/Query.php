<?php

namespace App\Core\Database;

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
     * @param $model
     *
     * @throws \Exception
     */


    public function __construct($model)
    {
        if (empty($model) === true) {
            throw new \Exception('Model class must by defined in respository class');
        }

        if (empty($model::TABLE) === true) {
            throw new \Exception('Model table must by defined in %s', $model);
        }

        $this->statement = '';
        $this->table = $model::TABLE;
        $this->model = $model;
    }


    /**
     * Initiate a Select statement
     *
     * @param array|string $fields
     *
     * @return $this
     */
    public function select(array|string $fields = ''): self
    {
        $this->statement .= 'SELECT ';

        $fieldsStr = $fields;

        if (empty($fields) === true) {
            $this->statement .= '* ';
        }
        if (is_array($fields) === true) {
            $fieldsStr = implode(', ', $fields);
        }

        $this->statement .= $fieldsStr.' FROM '.$this->table;

        return $this;
    }


    /**
     * Generate an INSERT statement from object instance data
     *
     * @param $data
     *
     * @return void
     */
    public function insert($data)
    {
        $this->statement = 'INSERT INTO '.$this->table;

        $paramsNames = array_map(function ($element) {
            return ':'.$element;
        }, $data);
        $paramsValues = array_values($data);

        $this->parameters = array_map(null, $paramsNames, $paramsValues);

        $columns = ' ('.implode(', ', array_keys($data)).') ';

        $values = 'VALUES('.implode(', ', array_keys($this->parameters)).')';

        $this->statement .= $columns.$values;
    }


    /**
     * Describe table statement (get columns information)
     *
     * @return void
     */
    public function describe()
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
     * @return array
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }
}

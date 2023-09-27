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

    private $parameters = [];

    public function __construct($model)
    {
        if (empty($model)) {
            throw new \Exception('Model class must by defined in respository class');
        }

        if (empty($model::TABLE)) {
            throw new \Exception('Model table must by defined in %s', $model);
        }

        $this->statement = '';
        $this->table = $model::TABLE;
        $this->model = $model;
    }

    public function select(array|string $fields = ''): self
    {
        $this->statement .= 'SELECT ';

        if (empty($fields)) {
            $this->statement .= '* ';
        } else {
            if (is_array($fields)) {
                $fieldsStr = implode(', ', $fields);
            } else {
                $fieldsStr = $fields;
            }

            $this->statement .= $fieldsStr.' ';
        }

        $this->statement .= ' FROM '.$this->table;

        return $this;
    }

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

    public function describe()
    {
        $this->statement = 'DESCRIBE '.$this->table;
    }

    public function getStatement(): string
    {
        return $this->statement;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}

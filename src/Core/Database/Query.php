<?php

namespace App\Core\Database;

use App\Model\Trait\SoftDeleteTrait;
use Exception;

class Query
{

    public const GET_FIRST = 1;

    public const GET_LAST = 2;

    /**
     * @var string
     */
    private string $verb;

    /**
     * @var array
     */
    private array $where = [];

    /**
     * @var array
     */
    private array $select = [];

    /**
     * @var array
     */
    private array $leftJoin = [];

    /**
     * @var int|null
     */
    private ?int $firstOrLast = null;

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
     * @var bool
     */
    private ?bool $withTrashed = null;

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

        $this->verb = '';
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
    public function select(): self
    {
        foreach (Database::getTableFields($this->model) as $field) {
            $this->select[$this->table.'.'.$field] = $field.'_'.count($this->select);
        }

        $this->verb = 'SELECT';

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
        $parameterName = ':'.str_replace('.', '_', $column).'_'.count($this->where);

        $this->where[] = [
            'column' => $column,
            'comparator' => $comparator,
            'parameterName' => $parameterName
        ];

        if ($value instanceof \DateTimeInterface) {
            $value = $value->format('Y-m-d H:i:s');
        }

        $this->parameters[$parameterName] = $value;

        return $this;
    }

    /**
     * Add a left join in the statement
     *
     * @param string $joinedModel
     * @param $fieldName
     *
     * @return void
     * @throws Exception
     */
    public function leftJoin(string $joinedModel, $fieldName)
    {
        $this->leftJoin[] = [
            'model' => $joinedModel,
            'field' => $fieldName
        ];

        foreach (Database::getTableFields($joinedModel) as $field) {
            $this->select[$joinedModel::TABLE.'.'.$field] = $field.'_'.count($this->select);
        }
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

        $this->verb = 'INSERT INTO '.$this->table;

        $columns = ' ('.implode(', ', array_keys($data)).') ';
        $values = 'VALUES('.implode(', ', array_keys($this->parameters)).')';

        $this->verb .= $columns.$values;
    }

    /**
     * Generate an UPDATE statement from object instance data
     *
     * @param array $data entity in array format
     * @param string $primaryKey name of the primary key
     *
     * @return void
     */
    public function updateOne(array $data, string $primaryKey): void
    {
        $this->setParameters($data);
        unset($data[$primaryKey]);

        $this->verb = 'UPDATE '.$this->table.' SET ';

        $columns = array_keys($data);

        for ($i = 0; $i < count($data); $i++) {
            if ($i > 0) {
                $this->verb .= ', ';
            }

            $this->verb .= $columns[$i].'= :'.$columns[$i];
        }

        $this->where = [
            [
                'column' => $primaryKey,
                'comparator' => '=',
                'parameterName' => ':'.$primaryKey
            ]
        ];
    }

    /**
     * Generate a DELETE statement to delete one entity
     *
     * @param string $primaryKey
     * @param mixed $value
     *
     * @return void
     */
    public function deleteOne(string $primaryKey, mixed $value)
    {
        $this->setParameters([$primaryKey => $value]);

        $this->verb = 'DELETE FROM '.$this->table;

        $this->where = [
            [
                'column' => $primaryKey,
                'comparator' => '=',
                'parameterName' => ':'.$primaryKey
            ]
        ];
    }

    /**
     * Get all data included deleted
     *
     * @return $this
     */
    public function withTrashed(): self
    {
        $this->withTrashed = true;
        return $this;
    }

    /**
     * Get Only first row of query result
     *
     * @return $this
     */
    public function first(): self
    {
        $this->firstOrLast = self::GET_FIRST;
        return $this;
    }

    /**
     * Get Only last row of query result
     *
     * @return $this
     */
    public function last(): self
    {
        $this->firstOrLast = self::GET_LAST;
        return $this;
    }

    public function getFirstOrLast(): ?int
    {
        return $this->firstOrLast;
    }

    /**
     * Describe table statement (get columns information)
     *
     * @return void
     */
    public function describe(): void
    {
        $this->verb = 'DESCRIBE '.$this->table;
    }


    /**
     * @return string
     */
    public function getStatement(): string
    {
        $statement = $this->verb;
        if ($this->verb === 'SELECT') {
            $statement .= ' '.implode(', ', array_map(
                    function ($alias, $field) {
                        return $field.' AS '.$alias;
                    },
                    $this->select,
                    array_keys($this->select)
                ));

            $statement .= ' FROM '.$this->table;
        }

        if (!empty($this->leftJoin)) {
            foreach ($this->leftJoin as $leftJoin) {
                $tableName = $leftJoin['model']::TABLE;
                $field = $leftJoin['field'];
                $statement .= ' LEFT JOIN '.$tableName.' ON '.$tableName.'.id = '.$this->table.'.'.$field.'_id';
            }
        }

        if (!empty($this->where) || $this->withTrashed === false) {
            $statement .= ' WHERE ';
            $wheres = $this->generateWheres();

            $statement .= implode(' AND ', $wheres);
        }


        $statement .= ';';
        return $statement;
    }

    /**
     * Generate a where clause string from current query object
     *
     * @return array
     */
    private function generateWheres(): array
    {
        $result = [];
        foreach ($this->where as $where) {
            $result[] = $where['column'].' '.$where['comparator'].' '.$where['parameterName'];
        }

        if ($this->withTrashed === false) {
            $result[] = $this->table.'.deleted_at IS NULL';
        }

        return $result;
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
    public function getSelect(): array
    {
        return $this->select;
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

    private function isModelSoftDeletable(): bool
    {
        $reflection = new \ReflectionClass($this->model);
        foreach ($reflection->getTraits() as $trait => $reflectionTrait) {
            if ($trait == SoftDeleteTrait::class) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get a list of queried joins
     *
     * @return array
     */
    public function getJoins(): array
    {
        return array_merge($this->leftJoin);
    }
}

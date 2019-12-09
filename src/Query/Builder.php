<?php

namespace Pbmengine\VideoApiClient\Query;

use Pbmengine\VideoApiClient\Query\Contracts\BuilderSerializer;

class Builder
{
    /** @var array|null */
    public $includes;

    /** @var array|null */
    public $columns;

    /** @var array|null */
    public $orders;

    /** @var array  */
    public $wheres = [];

    /** @var int|null */
    public $limit;

    /** @var int|null */
    public $offset;

    /** @var int|null */
    public $page;

    /** @var array  */
    public $operators = [
        '=', '<', '>', '<=', '>=', '<>', '!=', '<=>',
        'like', 'like binary', 'not like', 'ilike',
        '&', '|', '^', '<<', '>>',
        'rlike', 'not rlike', 'regexp', 'not regexp',
        '~', '~*', '!~', '!~*', 'similar to',
        'not similar to', 'not ilike', '~~*', '!~~*',
    ];

    /** @var BuilderSerializer */
    protected $serializer;

    /**
     * @return Builder
     */
    public static function query()
    {
        return new static();
    }

    /**
     * Add a basic or where clause to the query.
     *
     * @param  string|array|\Closure $column
     * @param  mixed $operator
     * @param  mixed $value
     * @return $this
     */
    public function orWhere($column, $operator = null, $value = null)
    {
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        return $this->where($column, $operator, $value, 'or');
    }

    /**
     * Add a basic where clause to the query.
     *
     * @param  string|array|\Closure $column
     * @param  mixed $operator
     * @param  mixed $value
     * @param string $boolean
     * @return $this
     */
    public function where($column, $operator = null, $value = null, $boolean = 'and')
    {
        // if column is an array we assume that there are where arrays
        // in form of [column, operator, value]
        if (is_array($column)) {
            return $this->addArrayOfWheres($column, $boolean);
        }

        // Here we will make some assumptions about the operator. If only 2 values are
        // passed to the method, we will assume that the operator is an equals sign
        // and keep going. Otherwise, we'll require the operator to be passed in.
        [$value, $operator] = $this->prepareValueAndOperator(
            $value, $operator, func_num_args() === 2
        );

        if ($column instanceof \Closure) {
            return $this->whereNested($column, $boolean);
        }

        $type = 'Basic';

        return $this->addWhere($type, $column, $operator, $value, $boolean);
    }

    /**
     * @param array $column
     * @param string $boolean
     * @param string $method
     * @return $this
     */
    protected function addArrayOfWheres(array $column, $boolean = 'and', $method = 'where')
    {
        return $this->whereNested(function($query) use ($column, $boolean, $method) {
            foreach ($column as $key => $value) {
                if (is_numeric($key) && is_array($value)) {
                    $query->{$method}(...array_values($value));
                } else {
                    $query->$method($key, '=', $value, $boolean);
                }
            }
        }, $boolean);
    }

    /**
     * @param Builder $query
     * @param string $boolean
     * @return $this
     */
    public function addNestedWhereQuery($query, $boolean = 'and')
    {
        if (count($query->wheres)) {
            $type = 'Nested';
            $query = $query->wheres;

            $this->wheres[] = [
                'type' => $type,
                'query' => $query,
                'boolean' => $boolean
            ];
        }

        return $this;
    }

    /**
     * @param \Closure $callback
     * @param string $boolean
     * @return Builder
     */
    protected function whereNested(\Closure $callback, $boolean = 'and')
    {
        call_user_func($callback, $query = $this->newQuery());

        return $this->addNestedWhereQuery($query, $boolean);
    }

    /**
     * @return Builder
     */
    public function newQuery()
    {
        return new static();
    }

    /**
     * @param $value
     * @param $operator
     * @param bool $useDefault
     * @return array
     */
    public function prepareValueAndOperator($value, $operator, $useDefault = false)
    {
        if ($useDefault) {
            return [$operator, '='];
        } elseif ($this->invalidOperatorAndValue($operator, $value)) {
            throw new \InvalidArgumentException('Illegal operator and value combination.');
        }

        return [$value, $operator];
    }

    /**
     * @param $operator
     * @param $value
     * @return bool
     */
    protected function invalidOperatorAndValue($operator, $value)
    {
        return is_null($value) && in_array($operator, $this->operators) &&
            !in_array($operator, ['=', '<>', '!=']);
    }

    /**
     * @param strign $type
     * @param $column
     * @param $operator
     * @param $value
     * @param string $boolean
     * @return $this
     */
    protected function addWhere($type, $column, $operator, $value, $boolean = 'and')
    {
        $this->wheres[] = [
            'type' => $type,
            'query' => [
                'field' => $column,
                'operator' => $operator,
                'value' => $value,
            ],
            'boolean' => $boolean
        ];

        return $this;
    }

    /**
     * @param string $operator
     *
     * @return mixed
     */
    protected function checkOperator($operator)
    {
        if (!in_array($operator, $this->operators)) {
            throw new \InvalidArgumentException("the operator {$operator} is not allowed");
        }

        return $operator;
    }

    /**
     * Set the columns to be selected.
     *
     * @param  array|mixed $columns
     * @return $this
     */
    public function select($columns = ['*'])
    {
        $this->columns = is_array($columns) ? $columns : func_get_args();

        return $this;
    }

    /**
     * Set the resources to be included.
     *
     * @param  array|mixed $resources
     * @return $this
     */
    public function include($resources = ['*'])
    {
        $this->includes = is_array($resources) ? $resources : func_get_args();

        return $this;
    }

    /**
     * Add an "order by" clause to the query.
     *
     * @param  string $column
     * @param  string $direction
     * @return $this
     *
     * @throws \InvalidArgumentException
     */
    public function orderBy($column, $direction = 'asc')
    {
        $direction = strtolower($direction);

        if (!in_array($direction, ['asc', 'desc'], true)) {
            throw new \InvalidArgumentException('Order direction must be "asc" or "desc".');
        }

        $this->orders[] = [
            'column' => $column,
            'direction' => $direction,
        ];

        return $this;
    }

    /**
     * @param $column
     * @return Builder
     */
    public function orderByDesc($column)
    {
        return $this->orderBy($column, 'desc');
    }

    /**
     * Alias to set the "limit" value of the query.
     *
     * @param  int $value
     * @return $this
     */
    public function take($value)
    {
        return $this->limit($value);
    }

    /**
     * @param $value
     * @return $this
     */
    public function limit($value)
    {
        if ($value >= 0) {
            $this->limit = $value;
        }

        return $this;
    }

    /**
     * Set the limit and offset for a given page.
     *
     * @param  int $page
     * @param  int $perPage
     * @return $this
     */
    public function forPage($page, $perPage = 15)
    {
        $this->page = $page;

        return $this->skip(($page - 1) * $perPage)->take($perPage);
    }

    public function page($page)
    {
        $this->page = $page;

        return $this;
    }

    /**
     * Alias to set the "offset" value of the query.
     *
     * @param  int $value
     * @return $this
     */
    public function skip($value)
    {
        return $this->offset($value);
    }


    /**
     * Set the "offset" value of the query.
     *
     * @param  int $value
     * @return $this
     */
    public function offset($value)
    {
        $this->offset = max(0, $value);

        return $this;
    }

    public function setSerializer(BuilderSerializer $serializer)
    {
        $this->serializer = $serializer;

        return $this;
    }

    public function toArray()
    {
        $this->serializer
            ->setBuilder($this)
            ->compileComponents()
            ->toArray();
    }

    /**
     * @return $this
     */
    public function get()
    {
        return $this;
    }

    public function toString()
    {
        $this->serializer
            ->setBuilder($this)
            ->compileComponents()
            ->toString();
    }
}

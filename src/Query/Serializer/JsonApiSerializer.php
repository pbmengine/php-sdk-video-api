<?php

namespace Pbmengine\VideoApiClient\Query\Serializer;

use Pbmengine\VideoApiClient\Query\Builder;
use Pbmengine\VideoApiClient\Query\Contracts\BuilderSerializer;
use Pbmengine\VideoApiClient\Query\Exceptions\InvalidDataException;

class JsonApiSerializer implements BuilderSerializer
{
    /** @var bool  */
    protected $withOperator = false;

    /** @var array */
    protected $queryComponents = [];

    /** @var string */
    protected $queryString = '';

    /** @var array  */
    protected $keyStringMappings = [
        'select' => 'fields',
        'where' => 'filter',
        'order' => 'sort',
        'limit' => 'page[size]',
        'page' => 'page[number]',
        'offset' => 'offset',
        'includes' => 'include'
    ];

    /** @var array  */
    protected $stringComponents = [
        'includes',
        'columns',
        'wheres',
        'orders',
        'limit',
        'page',
        'offset',
    ];

    /** @var Builder  */
    protected $builder;

    /**
     * JsonApiSerializer constructor.
     *
     * @param Builder|null $builder
     */
    public function __construct(Builder $builder = null)
    {
        if ($builder !== null) {
            $this->builder = $builder;
        }
    }

    /**
     * @param Builder $builder
     * @return $this
     */
    public function setBuilder(Builder $builder)
    {
        $this->builder = $builder;

        return $this;
    }

    public function compileComponents()
    {
        foreach ($this->stringComponents as $component) {
            $method = 'compile' . ucfirst($component);
            $this->$method();
        }

        $this->buildQueryString();

        return $this;
    }

    public function toArray(): array
    {
        return $this->queryComponents;
    }

    public function toString(): string
    {
        return $this->queryString;
    }

    public function withOperator(bool $value)
    {
        $this->withOperator = $value;

        return $this;
    }

    protected function buildQueryString()
    {
        if (! empty($this->queryComponents)) {
            $this->queryString = http_build_query(
                $this->queryComponents,
                null,
                '&'
            );
        }
    }

    public function compileLimit()
    {
        if (is_null($this->builder->limit)) {
            return $this;
        }

        $this->addToQueryComponents(
            $this->keyStringMappings['limit'],
            $this->builder->limit
        );

        return $this;
    }

    public function compileWheres()
    {
        if (is_null($this->builder->wheres) || empty($this->builder->wheres)) {
            return $this;
        }

        // only supporting Basic AND filter in form filter[key]=value
        foreach ($this->builder->wheres as $where) {

            if ($where['type'] !== 'Basic') {
                continue;
            }

            $expression = ($this->withOperator)
                ? "[{$where['query']['field']}][{$where['query']['operator']}]"
                : "[{$where['query']['field']}]";

            $this->addToQueryComponents(
                $this->keyStringMappings['where'] . $expression,
                $where['query']['value']
            );
        }

        return $this;
    }

    public function compileColumns()
    {
        if (is_null($this->builder->columns) || empty($this->builder->columns)) {
            return $this;
        }

        $this->addToQueryComponents(
            $this->keyStringMappings['select'],
            implode(",", $this->builder->columns)
        );

        return $this;
    }

    public function compileIncludes()
    {
        if (is_null($this->builder->includes) || empty($this->builder->includes)) {
            return $this;
        }

        $this->addToQueryComponents(
            $this->keyStringMappings['includes'],
            implode(",", $this->builder->includes)
        );

        return $this;
    }

    public function compileOrders()
    {
        if (is_null($this->builder->orders) || empty($this->builder->orders)) {
            return $this;
        }

        $orderValues = [];

        foreach ($this->builder->orders as $order) {

            if (! isset($order['column'])) {
                throw new InvalidDataException('key column in order is missing');
            }

            if (!isset($order['direction'])) {
                throw new InvalidDataException('key direction in order is missing');
            }

            if ($order['direction'] === 'desc') {
                $order['column'] = "-" . $order['column'];
            }

            $orderValues[] = $order['column'];
        }

        $this->addToQueryComponents(
            $this->keyStringMappings['order'],
            implode(',', $orderValues)
        );

        return $this;
    }

    public function compilePage()
    {
        if (is_null($this->builder->page) || $this->builder->page == 1) {
            return $this;
        }

        $this->addToQueryComponents(
            $this->keyStringMappings['page'],
            (int) $this->builder->page
        );

        return $this;
    }

    public function compileOffset()
    {
        if (is_null($this->builder->offset) || $this->builder->offset == 0) {
            return $this;
        }

        /*
         * no need currently
        $this->addToQueryComponents(
            $this->keyStringMappings['offset'],
            (int) $this->query->offset
        );
        */

        return $this;
    }

    protected function addToQueryComponents($key, $value)
    {
        $this->queryComponents[$key] = $value;
    }
}

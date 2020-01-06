<?php

namespace Pbmengine\VideoApiClient\Query\Contracts;

use Pbmengine\VideoApiClient\Query\Builder;

interface BuilderSerializer
{
    public function __construct(Builder $query);

    public function setBuilder(Builder $builder);

    public function toString(): string;

    public function compileIncludes();

    public function compileOrders();

    public function compileWheres();

    public function compilePage();

    public function compileLimit();

    public function compileOffset();

    public function compileColumns();

    public function compileComponents();
}

<?php declare(strict_types=1);


namespace QueryBuilder\Expr;


class LeftJoin extends Join
{
    protected $tag = 'LEFT JOIN';
}
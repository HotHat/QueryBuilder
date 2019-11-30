<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class LeftJoin extends Join
{
    protected $tag = 'LEFT JOIN';
}
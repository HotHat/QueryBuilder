<?php declare(strict_types=1);


namespace QueryBuilder\Expr\aggregate;


use function QueryBuilder\Expr\wrapValue;

class Count extends Aggregate
{
    public function __construct()
    {
    }

    public function compile()
    {
        return sprintf('COUNT(*) as count');
    }
}
<?php declare(strict_types=1);


namespace QueryBuilder\Expr\aggregate;


use function QueryBuilder\Expr\wrapValue;

class Avg extends Aggregate
{
    public function compile()
    {
        return sprintf('AVG(%s) as avg', wrapValue($this->column));
    }
}
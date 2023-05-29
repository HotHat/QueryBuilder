<?php declare(strict_types=1);


namespace QueryBuilder\Expr\aggregate;


use function QueryBuilder\Expr\wrapValue;

class Max extends Aggregate
{
    public function compile()
    {
        return sprintf('MAX(%s) as max', wrapValue($this->column));
    }
}
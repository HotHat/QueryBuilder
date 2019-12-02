<?php declare(strict_types=1);


namespace SqlBuilder\Expr\aggregate;


use function SqlBuilder\Expr\wrapValue;

class Avg extends Aggregate
{
    public function compile()
    {
        return sprintf('AVG(%s) as avg', wrapValue($this->column));
    }
}
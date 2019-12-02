<?php declare(strict_types=1);


namespace SqlBuilder\Expr\aggregate;


use function SqlBuilder\Expr\wrapValue;

class Max extends Aggregate
{
    public function compile()
    {
        return sprintf('MAX(%s) as max', wrapValue($this->column));
    }
}
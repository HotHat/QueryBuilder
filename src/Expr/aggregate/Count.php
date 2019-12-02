<?php declare(strict_types=1);


namespace SqlBuilder\Expr\aggregate;


use function SqlBuilder\Expr\wrapValue;

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
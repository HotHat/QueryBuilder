<?php declare(strict_types=1);


namespace QueryBuilder\Expr\aggregate;


use QueryBuilder\Expr\CompileToPair;

abstract class Aggregate implements CompileToPair
{
    protected $column;

    public function __construct($column)
    {

        $this->column = $column;
    }

}
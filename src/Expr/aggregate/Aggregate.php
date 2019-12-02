<?php declare(strict_types=1);


namespace SqlBuilder\Expr\aggregate;


use SqlBuilder\Expr\CompileToPair;

abstract class Aggregate implements CompileToPair
{
    protected $column;

    public function __construct($column)
    {

        $this->column = $column;
    }

}
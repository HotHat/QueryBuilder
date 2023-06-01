<?php declare(strict_types=1);


namespace QueryBuilder\Expr\Aggregate;


use QueryBuilder\Expr\CompileToString;

abstract class Aggregate implements CompileToString
{
    protected $column;

    public function __construct($column)
    {

        $this->column = $column;
    }

}
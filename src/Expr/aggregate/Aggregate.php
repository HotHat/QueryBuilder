<?php declare(strict_types=1);


namespace SqlBuilder\Expr\aggregate;


use SqlBuilder\Expr\Parse;

abstract class Aggregate implements Parse
{
    protected $column;

    public function __construct($column)
    {

        $this->column = $column;
    }

}
<?php declare(strict_types=1);


namespace QueryBuilder\Expr;


class OrderByItem
{
    public $column;
    public $direction;

    public function __construct($column, $direction)
    {

        $this->column = $column;
        $this->direction = $direction;
    }

}
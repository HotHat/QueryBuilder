<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class InsertExpr
{
    private $table;
    private $values;

    public function __construct($table, $values)
    {
        $this->table = $table;
        $this->values = $values;
    }
}
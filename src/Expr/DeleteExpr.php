<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class DeleteExpr implements Parse
{
    private $table;
    private $from;
    private $where;

    public function __construct($table, $from, $where)
    {
        $this->table = $table;
        $this->from = $from;
        $this->where = $where;
    }

    public function compile() {

    }

}
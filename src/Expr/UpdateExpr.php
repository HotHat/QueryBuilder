<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class UpdateExpr
{
    private $table;
    private $set;
    private $where;
    private $orderBy;
    private $limit;

    public function __construct($table, $set, $where, $orderBy, $limit)
    {
        $this->table = $table;
        $this->set = $set;
        $this->where = $where;
        $this->orderBy = $orderBy;
        $this->limit = $limit;
    }

}
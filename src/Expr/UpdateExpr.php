<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class UpdateExpr implements Parse
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

    public function compile(): array
    {
        $sql = trim(sprintf('%s%s%s%s%s',
            $this->compileUpdate(),
            $this->compileSet(),
            $this->compileWhere(),
            $this->compileOrderBy(),
            $this->compileLimit(),
        ));
        return [$sql, $this->bindValue];
    }

}
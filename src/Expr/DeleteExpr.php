<?php declare(strict_types=1);


namespace QueryBuilder\Expr;


class DeleteExpr implements CompileToPair
{
    private $table;
    private $where;

    public function __construct($table, $where)
    {
        $this->table = $table;
        $this->where = $where;
    }

    public function compile() : array {
        [$sqlWhere, $bindValue] = $this->where->compile();

        $sql = trim(sprintf('DELETE FROM%s%s',
            $this->table->compile(),
            $sqlWhere,
        ));
        return [$sql, $bindValue];

    }

}
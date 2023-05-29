<?php declare(strict_types=1);


namespace QueryBuilder\Expr;


class UpdateExpr implements CompileToPair
{
    private $table;
    private $set;
    private $where;
    private $orderBy;
    private $limit;

    public function __construct(Table $table, UpdatePair $set, WhereCondition $where, OrderBy $orderBy, Limit $limit)
    {
        $this->table = $table;
        $this->set = $set;
        $this->where = $where;
        $this->orderBy = $orderBy;
        $this->limit = $limit;
    }

    public function compile(): array
    {
        [$sqlSet, $bindValue] = $this->set->compile();
        [$sqlWhere, $value] = $this->where->compile();
        $bindValue = array_merge($bindValue, $value);

        $sql = trim(sprintf('UPDATE%s SET %s%s%s%s',
            $this->table->compile(),
            $sqlSet,
            $sqlWhere,
            $this->orderBy->compile(),
            $this->limit->compile(),
        ));
        return [$sql, $bindValue];
    }

}
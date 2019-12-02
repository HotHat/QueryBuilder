<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class UpdateExpr implements Parse
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
        [$sql, $bindValue] = $this->where->compile();

        $sql = trim(sprintf('UPDATE%sSET %s%s%s%s',
            $this->table->compile(),
            $this->set->compile(),
            $sql,
            $this->orderBy->compile(),
            $this->limit->compile(),
        ));
        return [$sql, $bindValue];
    }

}
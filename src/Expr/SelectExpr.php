<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class SelectExpr
{
    private $select;
    private $from;
    private $where;
    private $groupBy;
    private $having;
    private $orderBy;
    private $limit;
    private $forLock;
    private $union;

    public function __construct(
        Select $select, Table $from, WhereCondition $where,
        GroupBy $groupBy, HavingCondition $having, OrderBy $orderBy,
        Limit $limit, ForLock $forLock, Union $union)
    {

        $this->select = $select;
        $this->from = $from;
        $this->where = $where;
        $this->groupBy = $groupBy;
        $this->having = $having;
        $this->orderBy = $orderBy;
        $this->limit = $limit;
        $this->forLock = $forLock;
        $this->union = $union;
    }

    public function compile() :array
    {
        [$whereSql, $value] = $this->where->compile();
        [$havingSql, $value2] = $this->having->compile();

        [$unionSql, $value3] = $this->union->compile();

        $bindValue = array_merge($value, $value2, $value3);

        $sql = trim(sprintf('%s%s%s%s%s%s%s%s%s',
            $this->select->compile(),
            $this->from->compile(),
            $whereSql,
            $this->groupBy->compile(),
            $havingSql,
            $this->orderBy->compile(),
            $this->limit->compile(),
            $this->forLock->compile(),
            $unionSql
        ));

        return [$sql, $bindValue];
    }


}
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
    private $forUpdate;

    public function __construct(
        Select $select, From $from, WhereCondition $where,
        GroupBy $groupBy, Having $having, OrderBy $orderBy,
        Limit $limit, ForUpdate $forUpdate)
    {

        $this->select = $select;
        $this->from = $from;
        $this->where = $where;
        $this->groupBy = $groupBy;
        $this->having = $having;
        $this->orderBy = $orderBy;
        $this->limit = $limit;
        $this->forUpdate = $forUpdate;
    }

    public function compile() :array
    {
        [$whereSql, $value] = $this->where->compile();
        [$havingSql, $value2] = $this->having->compile();
        $bindValue = $value;
        $bindValue = array_merge($bindValue, $value2);

        $sql = sprintf('%s%s%s%s%s%s%s%s',
            $this->select->compile(),
            $this->from->compile(),
            $whereSql,
            $this->groupBy->compile(),
            $havingSql,
            $this->orderBy->compile(),
            $this->limit->compile(),
            $this->forUpdate->compile(),
        );

        return [$sql, $bindValue];
    }


}
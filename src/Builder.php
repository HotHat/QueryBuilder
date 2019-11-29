<?php declare(strict_types=1);


namespace SqlBuilder;


use SqlBuilder\Expr\ForShare;
use SqlBuilder\Expr\ForUpdate;
use SqlBuilder\Expr\From;
use SqlBuilder\Expr\GroupBy;
use SqlBuilder\Expr\Having;
use SqlBuilder\Expr\Limit;
use SqlBuilder\Expr\OrderBy;
use SqlBuilder\Expr\OrderByItem;
use SqlBuilder\Expr\OrWhere;
use SqlBuilder\Expr\OrWhereGroup;
use SqlBuilder\Expr\Select;
use SqlBuilder\Expr\Select as SelectClause;
use SqlBuilder\Expr\SelectCompile;
use SqlBuilder\Expr\SelectExpr;
use SqlBuilder\Expr\Set;
use SqlBuilder\Expr\Table;
use SqlBuilder\Expr\UpdateCompile;
use SqlBuilder\Expr\UpdateExpr;
use SqlBuilder\Expr\Where;
use SqlBuilder\Expr\WhereCondition;
use SqlBuilder\Expr\WhereGroup;

class Builder
{
    protected $container = [
        'table',
        'from',
        'where',
        'values',
        'select',
        'groupBy',
        'having',
        'orderBy',
        'limit',
        'forUpdate',
        'updateSet',
    ];
    protected $stack;
    protected $isInStack;

    public function __construct()
    {
        $this->container = [
            'table' => new Table(),
            'from' => new From(),
            'where' => new WhereCondition(),
            'values' => [],
            'select' => new Select(),
            'groupBy' => new GroupBy(),
            'having' => new Having(),
            'orderBy' => new OrderBy(),
            'limit' => new Limit(),
            'forUpdate' => new ForUpdate(),
            'updateSet' => new Set(),
        ];
        $this->stack = [];
        $this->isInStack = false;
    }

    public function select(...$column)
    {
        foreach ($column as $it) {
            $this->container['select']->addItem($it);
        }

        return $this;
    }

    public function table(...$table) {
        foreach ($table as $it) {
            $this->container['table']->addItem($it);
        }

        return $this;
    }

    public function from(...$table) {
        return $this->table(...$table);
    }

    public function where(...$where)
    {
        if (is_callable($where[0])) {
            $fn = $where[0];
            $this->isInStack = true;

            $fn($this);

            $whereGroup = new WhereGroup();
            foreach ($this->stack as $it) {
                $whereGroup->addWhere($it);
            }

            $this->stack = [];
            $this->isInStack = false;

            $this->container['where']->addWhere($whereGroup);

            return $this;
        }

        $where = new Where($where[0], $where[1] ?? null, $where[2] ?? null);

        if ($this->isInStack) {
            $this->stack[] = $where;

        } else {
            $this->container['where']->addWhere($where);
        }

        return $this;

    }

    public function orWhere(...$where)
    {
        if (is_callable($where[0])) {
            $fn = $where[0];
            $this->isInStack = true;

            $fn($this);

            $whereGroup = new OrWhereGroup();
            foreach ($this->stack as $it) {
                $whereGroup->addWhere($it);
            }

            $this->stack = [];
            $this->isInStack = false;

            $this->container['where']->addWhere($whereGroup);

            return $this;
        }

        $where = new OrWhere($where[0], $where[1] ?? null, $where[2] ?? null);

        if ($this->isInStack) {
            $this->stack[] = $where;

        } else {
            $this->container['where']->addWhere($where);
        }

        return $this;
    }

    public function having(...$where) {
        $where = new Having($where[0], $where[1] ?? null, $where[2] ?? null);

        if ($this->isInStack) {
            $this->stack[] = $where;
        } else {
            $this->container['having']->addWhere($where);
        }

        return $this;
    }

    public function update($data)
    {
        foreach ($data as $k => $v) {
            $this->container['updateSet']->addItem([$k, $v]);
        }

        $expr = new UpdateExpr($this->container['table'],
        $this->container['updateSet'],
            $this->container['where'],
            $this->container['orderBy'],
            $this->container['limit'],
        );

        return $expr->compile();

    }

    public function get() {

        $expr = new SelectExpr(
            $this->container['select'],
            $this->container['table']->toFrom(),
            $this->container['where'],
            $this->container['groupBy'],
            $this->container['having'],
            $this->container['orderBy'],
            $this->container['limit'],
            $this->container['forUpdate']
        );

        $result = $expr->compile();

        return $result;

    }

    public function limit($offset, $row = '') {
        $this->container['limit']->addItem($offset);

        if (!empty($row)) {
            $this->container['limit']->addItem($row);
        }

        return $this;
    }

    public function forUpdate() {
        $this->container['forUpdate'] = new ForUpdate();
        return $this;
    }

    public function forShare() {
        $this->container['forUpdate'] = new ForShare();
        return $this;
    }

    public function orderBy($name, $direction = '') {
        $orderBy = new OrderByItem($name, $direction);

        $this->container['orderBy']->addItem($orderBy);

        return $this;
    }

}
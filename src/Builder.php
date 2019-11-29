<?php declare(strict_types=1);

namespace SqlBuilder;
use SqlBuilder\Expr\ForShare;
use SqlBuilder\Expr\ForUpdate;
use SqlBuilder\Expr\From;
use SqlBuilder\Expr\Having;
use SqlBuilder\Expr\Limit;
use SqlBuilder\Expr\OrderBy;
use SqlBuilder\Expr\OrderByItem;
use SqlBuilder\Expr\OrWhere;
use SqlBuilder\Expr\OrWhereGroup;
use SqlBuilder\Expr\Select as SelectClause;
use SqlBuilder\Expr\SelectCompile;
use SqlBuilder\Expr\Set;
use SqlBuilder\Expr\Update;
use SqlBuilder\Expr\UpdateCompile;
use SqlBuilder\Expr\Where;
use SqlBuilder\Expr\WhereGroup;

class Builder
{
    protected $container;
    protected $bindValue;
    protected $stack;
    protected $isInStack;

    public function __construct($container = [], $bindValue = [], $stack = [], $isInStack = false)
    {
        $this->container = $container;
        $this->bindValue = $bindValue;
        $this->stack = $stack;
        $this->isInStack = $isInStack;
    }

    public function select(...$column)
    {
        $select = new SelectClause();
        foreach ($column as $it) {
            $select->addItem($it);
        }

        $this->container[] = $select;

        return $this;
    }

    public function table(...$table) {
        $from = new From();
        foreach ($table as $it) {
            $from->addItem($it);
        }

        $this->container[] = $from;

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

            $this->container[] = $whereGroup;

            return $this;
        }

        $where = new Where($where[0], $where[1] ?? null, $where[2] ?? null);

        if ($this->isInStack) {
            $this->stack[] = $where;

        } else {
            $this->container[] = $where;
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

            $this->container[] = $whereGroup;

            return $this;
        }

        $where = new OrWhere($where[0], $where[1] ?? null, $where[2] ?? null);

        if ($this->isInStack) {
            $this->stack[] = $where;

        } else {
            $this->container[] = $where;
        }

        return $this;
    }

    public function having(...$where) {
        $where = new Having($where[0], $where[1] ?? null, $where[2] ?? null);

        if ($this->isInStack) {
            $this->stack[] = $where;

        } else {
            $this->container[] = $where;
        }

        return $this;
    }

    public function update($data)
    {
        $update = new Set();
        foreach ($data as $k => $v) {
            $update->addItem([$k, $v]);
        }

        $this->container[] = $update;


        $compile = new UpdateCompile($this->container);
        $result = $compile->compile();

        $this->reset();

        return $result;

    }

    public function get() {
        $compile = new SelectCompile($this->container);
        $result = $compile->compile();

        $this->reset();
        return $result;

    }

    public function limit($offset, $row = '') {
        $limit = new Limit();
        $limit->addItem($offset);

        if (!empty($row)) {
            $limit->addItem($row);
        }

        $this->container[] = $limit;

        return $this;
    }

    public function forUpdate() {
        $this->container[] = new ForUpdate();
        return $this;
    }

    public function forShare() {
        $this->container[] = new ForShare();
        return $this;
    }

    public function orderBy($name, $direction = '') {
        $orderBy = new OrderByItem($name, $direction);

        $this->container[] = $orderBy;

        return $this;
    }

    private function reset() {
        $this->container = [];
        $this->bindValue = [];
        $this->stack = [];
        $this->isInStack = false;
    }
}
<?php declare(strict_types=1);


namespace SqlBuilder;


use SqlBuilder\Expr\From;
use SqlBuilder\Expr\OrWhere;
use SqlBuilder\Expr\OrWhereGroup;
use SqlBuilder\Expr\Select as SelectClause;
use SqlBuilder\Expr\Where;
use SqlBuilder\Expr\WhereCondition;
use SqlBuilder\Expr\WhereGroup;

abstract class AbstractBuilder
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

    public function table(...$table) : AbstractBuilder {
        $from = new From();
        foreach ($table as $it) {
            $from->addItem($it);
        }

        $this->container[] = $from;

        return $this;
    }

    public function from(...$table) : AbstractBuilder {
        return $this->table(...$table);
    }

    protected function compileFrom()
    {
        foreach ($this->container as $it) {
            if ($it instanceof \SqlBuilder\Expr\From) {
                return $it->compile();
            }
        }

        throw new BuilderException('Not find from statement');
    }


    public function where(...$where) : AbstractBuilder
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

    public function orWhere(...$where) : AbstractBuilder
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


    protected function compileWhere()
    {
        $condition = new WhereCondition();
        foreach ($this->container as $it) {
            if ($it instanceof \SqlBuilder\Expr\Conjunct) {
                $condition->addWhere($it);
            }
        }

        [$sql, $value] = $condition->compile();

        $this->bindValue = array_merge($this->bindValue, $value);

        return empty($sql) ? '' : sprintf('WHERE %s', $sql);

    }

}
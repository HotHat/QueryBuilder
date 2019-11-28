<?php declare(strict_types=1);


namespace SqlBuilder;


use SqlBuilder\scheme\From;
use SqlBuilder\scheme\OrWhere;
use SqlBuilder\scheme\OrWhereGroup;
use SqlBuilder\scheme\Select as SelectClause;
use SqlBuilder\scheme\Where;
use SqlBuilder\scheme\WhereCondition;
use SqlBuilder\scheme\WhereGroup;

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

        return new SelectBuilder($this->container, $this->bindValue, $this->stack, $this->isInStack);
    }

    public function table(...$table) : AbstractBuilder {
        $from = new From();
        foreach ($table as $it) {
            $from->addItem($it);
        }

        $this->container[] = $from;

        return new SelectBuilder($this->container, $this->bindValue, $this->stack, $this->isInStack);
    }

    public function from(...$table) : AbstractBuilder {
        return $this->table(...$table);
    }


    public function orderBy()
    {

    }

    public function having()
    {

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

    public function groupBy()
    {

    }

    public function limit() {

    }

    public function forUpdate()
    {

    }

    public function lock()
    {

    }

}
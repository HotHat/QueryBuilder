<?php declare(strict_types=1);

namespace ZiWen\SqlBuilder;
use ZiWen\SqlBuilder\scheme\Select as SelectClause;

class Builder
{


    private $container;

    public function __construct()
    {

    }

    public function select(...$column)
    {
        $select = new SelectClause();
        foreach ($column as $it) {
            $select->addItem($it);
        }

        $this->container[] = $select;

        return new SelectStatement($this->container);
    }

    public function from(...$table)
    {
        return $this;
    }


    public function orderBy()
    {

    }

    public function having()
    {

    }

    public function getSql() {

    }

    public function where(...$params)
    {

    }

    public function orWhere()
    {

    }

    public function groupBy()
    {

    }

    public function limit()
    {

    }

    public function forUpdate()
    {

    }

    public function lock()
    {

    }

}
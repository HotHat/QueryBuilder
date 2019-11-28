<?php declare(strict_types=1);

namespace SqlBuilder;
use SqlBuilder\scheme\Select as SelectClause;

class Builder extends AbstractBuilder
{

    public function update()
    {

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

    public function get() {

    }

}
<?php declare(strict_types=1);

namespace SqlBuilder;
use SqlBuilder\scheme\Select as SelectClause;
use SqlBuilder\scheme\Set;
use SqlBuilder\scheme\Update;

class Builder extends AbstractBuilder
{

    public function update($data)
    {
        $update = new Set();
        foreach ($data as $k => $v) {
            $update->addItem([$k, $v]);
        }

        $this->container[] = $update;


        $compile = new UpdateCompile($this->container);
        $result = $compile->compile();

        return $result;

    }

    public function get() {
        $compile = new SelectCompile($this->container);
        $result = $compile->compile();

        return $result;

    }
}
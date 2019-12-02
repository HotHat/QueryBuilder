<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class InsertValue extends Column
{
    public function compile(): string
    {
        $cols = [];
        $values = [];
        foreach ($this->container as $it) {
            $item = $it->getValue();
            $cols[] = $item[0];
            $values[] = $item[1];
        }

        $cols = array_map(function ($it) {
            return wrapValue($it);
        }, $cols);

        $values = array_map(function ($it) {
            return sprintf("'%s'", $it);
        }, $values);

        return sprintf('(%s) VALUES (%s)', implode(', ', $cols), implode(', ', $values));
    }

}
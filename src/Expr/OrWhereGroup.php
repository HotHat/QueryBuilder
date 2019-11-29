<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class OrWhereGroup extends WhereGroup
{

    public function isAnd() {
        return false;
    }

}
<?php declare(strict_types=1);


namespace QueryBuilder\Expr;


class OrWhere extends WhereItem
{


    public function isAnd(): bool
    {
        return false;
    }
}
<?php declare(strict_types=1);


namespace QueryBuilder\Expr;


class Where extends WhereItem
{

    public function isAnd(): bool
    {
        return true;
    }
}
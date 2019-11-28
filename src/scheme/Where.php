<?php declare(strict_types=1);


namespace SqlBuilder\scheme;


class Where extends WhereItem
{

    public function isAnd(): bool
    {
        return true;
    }
}
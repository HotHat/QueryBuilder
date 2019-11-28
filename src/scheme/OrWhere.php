<?php declare(strict_types=1);


namespace SqlBuilder\scheme;


class OrWhere extends WhereItem
{


    public function isAnd(): bool
    {
        return false;
    }
}
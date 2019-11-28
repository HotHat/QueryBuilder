<?php declare(strict_types=1);


namespace SqlBuilder\scheme;


class OrWhereGroup extends WhereGroup
{

    public function isAnd() {
        return false;
    }

}
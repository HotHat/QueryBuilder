<?php declare(strict_types=1);


namespace ZiWen\SqlBuilder\scheme;


class OrWhereGroup extends WhereGroup
{

    public function isAnd() {
        return false;
    }

}
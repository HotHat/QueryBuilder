<?php declare(strict_types=1);


namespace QueryBuilder\Expr;


class Limit extends Column
{
    protected $tag = 'LIMIT';

    public function compile() : string {
        return compileToString($this);
    }

}
<?php declare(strict_types=1);


namespace QueryBuilder\Expr;


class GroupBy extends Column implements CompileToString
{
    protected $tag = 'GROUP BY';



    public function compile()
    {
        return compileToString($this);
    }
}
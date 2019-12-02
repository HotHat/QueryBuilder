<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class ForUpdate extends Column implements CompileToString
{
    public function compile(): string
    {
        return compileWithDefault($this->isEmpty(), function () {
            return 'FOR UPDATE';
        });
    }

}
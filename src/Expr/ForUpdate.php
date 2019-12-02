<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class ForUpdate extends Column
{
    public function compile(): string
    {
        return compileWithDefault($this->isEmpty(), function () {
            return 'FOR UPDATE';
        });
    }

}
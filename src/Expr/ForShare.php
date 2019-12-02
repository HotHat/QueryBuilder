<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class ForShare extends Column implements CompileToString
{
    public function compile(): string
    {
        return 'FOR SHARE';
    }

}
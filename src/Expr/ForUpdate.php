<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class ForUpdate extends Column
{
    public function compile(): string
    {
        return $this->emptyHandle(function() {
            return 'FOR UPDATE';
        });
    }

}
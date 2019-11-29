<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class Set extends Column
{
    protected $tag = 'SET';

    public function compile(): string
    {
        $this->container = array_map(function ($it) {
            return sprintf("%s%s%s='%s'", $this->escapeCode(), $it[0], $this->escapeCode(), $it[1]);
        }, $this->container);

        $this->escape = false;

        return parent::compile();
    }

}
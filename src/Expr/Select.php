<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class Select extends Column
{
    protected $tag = 'SELECT';


    public function compile() : string
    {
        if (empty($this->container)) {
            return 'SELECT *';
        }

        return parent::compile();
    }

}
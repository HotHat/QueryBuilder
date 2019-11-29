<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class Limit extends Column
{
    protected $tag = 'LIMIT';
    protected $escape = false;

}
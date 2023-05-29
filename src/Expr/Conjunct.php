<?php declare(strict_types=1);


namespace QueryBuilder\Expr;


interface Conjunct
{
    public function isAnd();
    public function compile() : array;

}
<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


interface CompileToPair
{
    /**
     * @return array [$sql, $bindValue]
     */
    public function compile() : array ;
}
<?php declare(strict_types=1);


namespace QueryBuilder\Expr;


class HavingCondition extends WhereCondition
{
    protected $tag = 'HAVING';
    //public function compile(): array
    //{
    //    [$sql, $value] = parent::compile();
    //    return compileWithDefault(empty($sql), function () use ($sql, $value) {
    //        return [prefixSpace(sprintf('HAVING %s', $sql)), $value];
    //    }, ['', []]);
    //}
    
}
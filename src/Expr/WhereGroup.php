<?php declare(strict_types=1);


namespace QueryBuilder\Expr;


class WhereGroup extends WhereCondition implements Conjunct
{

    public function isAnd() {
        return true;
    }

    public function compile(): array
    {
        [$sql, $value] = parent::compile();
        $sql = sprintf('%s%s%s', '(', $sql, ')');

        return [$sql, $value];
    }

}
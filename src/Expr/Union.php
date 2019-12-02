<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class Union extends Column implements CompileToPair
{
    public function compile(): array
    {
        $sqlArray = $bindValue = [];

        array_map(function (Value $it) use (&$sqlArray, &$bindValue) {
            $builder = $it->getValue();
            [$sql, $value] = $builder->get();
            $sqlArray[] = sprintf(' UNION %s', $sql);
            $bindValue[] = $value;
        }, $this->getContainer());

        return [
            implode(' ', $sqlArray),
            empty($bindValue) ? $bindValue : array_merge(...$bindValue)
        ];


    }
}
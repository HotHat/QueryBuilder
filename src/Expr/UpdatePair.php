<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class UpdatePair extends Column
{

    public function compile(): string
    {
        $s = array_map(function (Value $it) {
            return $it->toString(function ($it) {
                $v = $it->getValue();
                return sprintf("%s='%s'", wrapValue($v[0]), $v[1]);
            });
        }, $this->container);


        return sprintf('%s', implode(', ', $s));
    }

}
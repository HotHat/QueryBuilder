<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class UpdatePair extends Column
{

    public function compile(): array
    {
        $k = $v = [];
        foreach ($this->getContainer() as $it) {
            $item = $it->getValue();
            $k[] = $item[0];
            $v[] = $item[1];
        }

        $s = array_map(function ($it) {
            return sprintf('%s=?', wrapValue($it));
        }, $k);


        return [
            sprintf('%s', implode(', ', $s)),
            $v
        ];
    }

}
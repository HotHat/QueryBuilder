<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class Table extends Column
{
    protected $tag = '';

    public function toFrom() {
        $from = new From();

        foreach ($this->container as $it) {
            $from->addItem($it);
        }

        return $from;
    }
}
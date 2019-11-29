<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class From extends Column
{
    protected $tag = 'FROM';

    public function toTable() {
        $table = new Table();

        foreach ($this->container as $it) {
            $table->addItem($it);
        }

        return $table;
    }
}
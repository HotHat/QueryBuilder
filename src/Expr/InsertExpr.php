<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class InsertExpr implements Parse
{
    private $table;
    private $values;

    public function __construct($table, $values)
    {
        $this->table = $table;
        $this->values = $values;
    }

    public function compile() : array {

        $sql = trim(sprintf('INSERT%s%s',
            $this->table->compile(),
            $this->values->compile(),
        ));
        return [$sql, []];
    }
}
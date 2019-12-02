<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class InsertExpr implements CompileToPair
{
    private $table;
    private $values;

    public function __construct($table, $values)
    {
        $this->table = $table;
        $this->values = $values;
    }

    public function compile() : array {


        [$sqlValue, $bindValue] = $this->values->compile();

        $sql = trim(sprintf('INSERT%s%s',
            $this->table->compile(),
            $sqlValue
        ));
        return [$sql, $bindValue];
    }
}
<?php declare(strict_types=1);

namespace SqlBuilder\Expr;
use SqlBuilder\Expr\From;
use SqlBuilder\Expr\OrWhereGroup;
use SqlBuilder\Expr\Parse;
use SqlBuilder\Expr\Select as SelectClause;
use SqlBuilder\Expr\Where;
use SqlBuilder\Expr\OrWhere;
use SqlBuilder\Expr\WhereCondition;
use SqlBuilder\Expr\WhereGroup;

class SelectCompile extends QueryCompile
{

    public function compile() :array
    {
        $sql = trim(sprintf('%s%s%s%s%s%s%s%s',
            $this->compileSelect(),
            $this->compileFrom(),
            $this->compileWhere(),
            $this->compileGroupBy(),
            $this->compileHaving(),
            $this->compileOrderBy(),
            $this->compileLimit(),
            $this->compileFor(),
        ));
        return [$sql, $this->bindValue];
    }


}
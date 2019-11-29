<?php declare(strict_types=1);

namespace SqlBuilder;
use SqlBuilder\Expr\From;
use SqlBuilder\Expr\OrWhereGroup;
use SqlBuilder\Expr\Parse;
use SqlBuilder\Expr\Select as SelectClause;
use SqlBuilder\Expr\Where;
use SqlBuilder\Expr\OrWhere;
use SqlBuilder\Expr\WhereCondition;
use SqlBuilder\Expr\WhereGroup;

class SelectCompile extends BuilderCompile
{



    public function compile() :array
    {
        $sql = trim(sprintf('%s %s %s %s %s %s %s %s %s',
            $this->compileSelect(),
            $this->compileFrom(),
            $this->compileWhere(),
            $this->compileGroupBy(),
            $this->compileHaving(),
            $this->compileOrderBy(),
            $this->compileLimit(),
            $this->compileForUpdate(),
            $this->compileLock(),
        ));
        return [$sql, $this->bindValue];
    }


}
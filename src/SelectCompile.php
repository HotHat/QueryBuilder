<?php declare(strict_types=1);

namespace SqlBuilder;
use SqlBuilder\scheme\From;
use SqlBuilder\scheme\OrWhereGroup;
use SqlBuilder\scheme\Parse;
use SqlBuilder\scheme\Select as SelectClause;
use SqlBuilder\scheme\Where;
use SqlBuilder\scheme\OrWhere;
use SqlBuilder\scheme\WhereCondition;
use SqlBuilder\scheme\WhereGroup;

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
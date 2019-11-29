<?php declare(strict_types=1);

namespace SqlBuilder;
use SqlBuilder\Expr\From;
use SqlBuilder\Expr\OrWhereGroup;
use SqlBuilder\Expr\Parse;
use SqlBuilder\Expr\Set;
use SqlBuilder\Expr\Where;
use SqlBuilder\Expr\OrWhere;
use SqlBuilder\Expr\WhereCondition;
use SqlBuilder\Expr\WhereGroup;
use SqlBuilder\Expr\Update;

class UpdateCompile extends BuilderCompile
{
    public function compile(): array
    {
        $sql = trim(sprintf('%s %s %s %s %s',
            $this->compileUpdate(),
            $this->compileSet(),
            $this->compileWhere(),
            $this->compileOrderBy(),
            $this->compileLimit(),
        ));
        return [$sql, $this->bindValue];
    }
}

<?php declare(strict_types=1);

namespace SqlBuilder;
use SqlBuilder\scheme\From;
use SqlBuilder\scheme\OrWhereGroup;
use SqlBuilder\scheme\Parse;
use SqlBuilder\scheme\Set;
use SqlBuilder\scheme\Where;
use SqlBuilder\scheme\OrWhere;
use SqlBuilder\scheme\WhereCondition;
use SqlBuilder\scheme\WhereGroup;
use SqlBuilder\scheme\Update;

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

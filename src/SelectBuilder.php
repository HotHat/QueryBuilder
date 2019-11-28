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

class SelectBuilder extends AbstractBuilder implements Parse
{
    public function select(...$column)
    {
        $select = new SelectClause();
        foreach ($column as $it) {
            $select->addItem($it);
        }

        $this->container[] = $select;

        return $this;
    }


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

    private function compileSelect()
    {

        foreach ($this->container as $it) {
            if ($it instanceof \SqlBuilder\scheme\Select) {
                return $it->compile();
            }
        }

        return 'SELECT *';
    }




    private function compileGroupBy()
    {

    }

    private function compileOrderBy()
    {

    }

    private function compileHaving()
    {

    }

    private function compileLimit()
    {

    }

    private function compileForUpdate()
    {

    }

    private function compileLock()
    {

    }
}
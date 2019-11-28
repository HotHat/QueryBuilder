<?php declare(strict_types=1);

namespace SqlBuilder;
use SqlBuilder\scheme\From;
use SqlBuilder\scheme\OrWhereGroup;
use SqlBuilder\scheme\Parse;
use SqlBuilder\scheme\Where;
use SqlBuilder\scheme\OrWhere;
use SqlBuilder\scheme\WhereCondition;
use SqlBuilder\scheme\WhereGroup;

class SelectBuilder extends AbstractBuilder implements Parse
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

    private function compileSelect()
    {

        foreach ($this->container as $it) {
            if ($it instanceof \SqlBuilder\scheme\Select) {
                return $it->compile();
            }
        }

        return 'SELECT *';
    }

    private function compileFrom()
    {
        foreach ($this->container as $it) {
            if ($it instanceof \SqlBuilder\scheme\From) {
                return $it->compile();
            }
        }

        throw new BuilderException('Not find from statement');
    }

    private function compileWhere()
    {
        $condition = new WhereCondition();
        foreach ($this->container as $it) {
            if ($it instanceof \SqlBuilder\scheme\Conjunct) {
                $condition->addWhere($it);
            }
        }

        [$sql, $value] = $condition->compile();

        $this->bindValue = array_merge($this->bindValue, $value);

        return empty($sql) ? '' : sprintf('WHERE %s', $sql);

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
<?php declare(strict_types=1);

namespace ZiWen\SqlBuilder;
use ZiWen\SqlBuilder\scheme\From;
use ZiWen\SqlBuilder\scheme\OrWhereGroup;
use ZiWen\SqlBuilder\scheme\Parse;
use ZiWen\SqlBuilder\scheme\Where;
use ZiWen\SqlBuilder\scheme\OrWhere;
use ZiWen\SqlBuilder\scheme\WhereCondition;
use ZiWen\SqlBuilder\scheme\WhereGroup;

class UpdateBuilder extends AbstractBuilder implements Parse
{


    public function compile() :array
    {
        $sql = trim(sprintf('%s %s %s %s',
            $this->compileFrom(),
            $this->compileWhere(),
            $this->compileOrderBy(),
            $this->compileLimit(),
        ));
        return [$sql, $this->bindValue];
    }


    private function compileFrom()
    {
        foreach ($this->container as $it) {
            if ($it instanceof \ZiWen\SqlBuilder\scheme\From) {
                return $it->compile();
            }
        }

        throw new \BuilderException('Not find from statement');
    }

    private function compileWhere()
    {
        $condition = new WhereCondition();
        foreach ($this->container as $it) {
            if ($it instanceof \ZiWen\SqlBuilder\scheme\Conjunct) {
                $condition->addWhere($it);
            }
        }

        [$sql, $value] = $condition->compile();

        $this->bindValue = array_merge($this->bindValue, $value);

        return sprintf('WHERE %s', $sql);

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
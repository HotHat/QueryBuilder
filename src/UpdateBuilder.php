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

class UpdateBuilder extends AbstractBuilder implements Parse
{
    function table(...$table): AbstractBuilder
    {
        $from = new Update();
        foreach ($table as $it) {
            $from->addItem($it);
        }

        $this->container[] = $from;

        return $this;
    }

    public function update($data) {
        $update = new Set();

        foreach ($data as $k => $v) {
            $update->addItem([$k, $v]);
        }

        $this->container[] = $update;
        return $this;
    }


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


    private function compileUpdate()
    {
        foreach ($this->container as $it) {
            if ($it instanceof \SqlBuilder\scheme\Update) {
                return $it->compile();
            }
        }

        throw new BuilderException('Without UPDATE statement');

    }


    private function compileOrderBy()
    {

    }


    private function compileLimit()
    {

    }

    private function compileSet()
    {
        foreach ($this->container as $it) {
            if ($it instanceof \SqlBuilder\scheme\Set) {
                return $it->compile();
            }
        }

        throw new BuilderException('Without SET statement');

    }
}

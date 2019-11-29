<?php declare(strict_types=1);


namespace SqlBuilder;


use SqlBuilder\Expr\Parse;
use SqlBuilder\Expr\WhereCondition;

abstract class BuilderCompile implements Parse
{
    protected $container;
    protected $bindValue;

    public function __construct($container)
    {
        $this->container = $container;
        $this->bindValue = [];
    }

    abstract function compile();

    protected function compileSelect()
    {

        foreach ($this->container as $it) {
            if ($it instanceof \SqlBuilder\Expr\Select) {
                return $it->compile();
            }
        }

        return 'SELECT *';
    }

    protected function compileWhere()
    {
        $condition = new WhereCondition();
        foreach ($this->container as $it) {
            if ($it instanceof \SqlBuilder\Expr\Conjunct) {
                $condition->addWhere($it);
            }
        }

        [$sql, $value] = $condition->compile();

        $this->bindValue = array_merge($this->bindValue, $value);

        return sprintf('WHERE %s', $sql);

    }


    protected function compileUpdate()
    {
        foreach ($this->container as $it) {
            if ($it instanceof \SqlBuilder\Expr\From) {
                $sql =  $it->compile();

                return preg_replace('/^FROM/i', 'UPDATE', $sql);

            }
        }

        throw new BuilderException('Without UPDATE statement');

    }

    protected function compileFrom()
    {
        foreach ($this->container as $it) {
            if ($it instanceof \SqlBuilder\Expr\From) {
                return $it->compile();
            }
        }

        throw new BuilderException('Without From statement');
    }

    protected function compileSet()
    {
        foreach ($this->container as $it) {
            if ($it instanceof \SqlBuilder\Expr\Set) {
                return $it->compile();
            }
        }

        throw new BuilderException('Without SET statement');

    }


    protected function compileGroupBy()
    {

    }

    protected function compileOrderBy()
    {

    }

    protected function compileHaving()
    {

    }

    protected function compileLimit()
    {

    }

    protected function compileForUpdate()
    {

    }

    protected function compileLock()
    {

    }

}
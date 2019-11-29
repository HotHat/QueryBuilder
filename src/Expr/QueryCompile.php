<?php declare(strict_types=1);


namespace SqlBuilder\Expr;

use function SqlBuilder\Expr\prefixSpace;

abstract class QueryCompile implements Parse
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
        return $this->compileHandler(Select::class, function (Parse $it) {
            return $it->compile();
        }, null, 'SELECT *');

    }

    private function compileHandler($class, $func, $exception = null, $default = '') {
        foreach ($this->container as $it) {
            if ($it instanceof $class) {
                return prefixSpace($func($it));
            }
        }

        if (!empty($exception)) {
            throw new ExprException($exception);
        }

        return $default;
    }

    protected function compileWhere()
    {
        $condition = new WhereCondition();
        foreach ($this->container as $it) {
            if ($it instanceof Conjunct && !($it instanceof Having)) {
                $condition->addWhere($it);
            }
        }

        [$sql, $value] = $condition->compile();

        $this->bindValue = array_merge($this->bindValue, $value);

        return empty($sql) ? '' : sprintf(' WHERE %s', $sql);

    }

    protected function compileHaving()
    {
        $condition = new HavingCondition();
        foreach ($this->container as $it) {
            if ($it instanceof \SqlBuilder\Expr\Having) {
                $condition->addWhere($it);
            }
        }

        [$sql, $value] = $condition->compile();

        $this->bindValue = array_merge($this->bindValue, $value);

        return empty($sql) ? '' : sprintf(' HAVING %s', $sql);

    }


    protected function compileUpdate()
    {
        return $this->compileHandler(From::class, function ($it) {

            $sql =  $it->compile();

            return preg_replace('/^FROM/i', 'UPDATE', $sql);

        }, 'Lack UPDATE statement');
    }

    protected function compileFrom()
    {
        return $this->compileHandler(From::class, function ($it) {
            return  $it->compile();
        }, 'Lack FROM statement');
    }

    protected function compileSet()
    {
        return $this->compileHandler(Set::class, function ($it) {
            return  $it->compile();
        }, 'Lack SET statement');
    }


    protected function compileGroupBy()
    {
        return $this->compileHandler(GroupBy::class, function ($it) {
            return  $it->compile();
        });
    }

    protected function compileOrderBy()
    {
        $orderBy = new OrderBy();
        foreach ($this->container as $it) {
            if ($it instanceof OrderByItem) {
                $orderBy->addItem($it);
            }
        }

        return prefixSpace($orderBy->compile());
    }


    protected function compileLimit()
    {
        return $this->compileHandler(Limit::class, function ($it) {
            return  $it->compile();
        });
    }

    protected function compileFor()
    {
        $lst = [];
        foreach ($this->container as $it) {
            if ($it instanceof ForShare || $it instanceof ForUpdate) {
                $lst[] = $it;
            }
        }
        return empty($lst) ? '' : prefixSpace(end($lst)->compile());
    }

}
<?php declare(strict_types=1);

namespace ZiWen\SqlBuilder;
use ZiWen\SqlBuilder\scheme\From;
use ZiWen\SqlBuilder\scheme\OrWhereGroup;
use ZiWen\SqlBuilder\scheme\Parse;
use ZiWen\SqlBuilder\scheme\Where;
use ZiWen\SqlBuilder\scheme\OrWhere;
use ZiWen\SqlBuilder\scheme\WhereCondition;
use ZiWen\SqlBuilder\scheme\WhereGroup;

class SelectStatement implements Parse
{
    private $container;
    private $bindValue;
    private $stack;
    private $isInStack;

    public function __construct($container = [])
    {
        $this->container = $container;
        $this->bindValue = [];
        $this->stack = [];
        $this->isInStack = false;
    }

    public function table(...$table) : SelectStatement {
        $from = new From();
        foreach ($table as $it) {
            $from->addItem($it);
        }

        $this->container[] = $from;

        return $this;
    }

    public function from(...$table) : SelectStatement {
        return $this->table(...$table);
    }


    public function orderBy()
    {

    }

    public function having()
    {

    }

    public function where(...$where) : SelectStatement
    {
        if (is_callable($where[0])) {
            $fn = $where[0];
            $this->isInStack = true;

            $fn($this);

            $whereGroup = new WhereGroup();
            foreach ($this->stack as $it) {
                $whereGroup->addWhere($it);
            }

            $this->stack = [];
            $this->isInStack = false;

            $this->container[] = $whereGroup;

            return $this;
        }

        $where = new Where($where[0], $where[1] ?? null, $where[2] ?? null);

        if ($this->isInStack) {
            $this->stack[] = $where;

        } else {
            $this->container[] = $where;
        }

        return $this;

    }

    public function orWhere(...$where)
    {
        if (is_callable($where[0])) {
            $fn = $where[0];
            $this->isInStack = true;

            $fn($this);

            $whereGroup = new OrWhereGroup();
            foreach ($this->stack as $it) {
                $whereGroup->addWhere($it);
            }

            $this->stack = [];
            $this->isInStack = false;

            $this->container[] = $whereGroup;

            return $this;
        }

        $where = new OrWhere($where[0], $where[1] ?? null, $where[2] ?? null);

        if ($this->isInStack) {
            $this->stack[] = $where;

        } else {
            $this->container[] = $where;
        }

        return $this;
    }

    public function groupBy()
    {

    }

    public function limit() {

    }

    public function forUpdate()
    {

    }

    public function lock()
    {

    }


    public function compile() : string
    {
        return trim(sprintf('%s %s %s %s %s %s %s %s %s',
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
    }

    private function compileSelect()
    {

        foreach ($this->container as $it) {
            if ($it instanceof \ZiWen\SqlBuilder\scheme\Select) {
                return $it->compile();
            }
        }

        return 'SELECT *';
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
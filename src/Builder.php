<?php declare(strict_types=1);


namespace SqlBuilder;


use SqlBuilder\Expr\aggregate\Avg;
use SqlBuilder\Expr\aggregate\Count;
use SqlBuilder\Expr\aggregate\Max;
use SqlBuilder\Expr\DeleteExpr;
use SqlBuilder\Expr\ExprException;
use SqlBuilder\Expr\ForShare;
use SqlBuilder\Expr\ForUpdate;
use SqlBuilder\Expr\GroupBy;
use SqlBuilder\Expr\Having;
use SqlBuilder\Expr\HavingCondition;
use SqlBuilder\Expr\InsertExpr;
use SqlBuilder\Expr\InsertValue;
use SqlBuilder\Expr\Join;
use SqlBuilder\Expr\LeftJoin;
use SqlBuilder\Expr\Limit;
use SqlBuilder\Expr\OrderBy;
use SqlBuilder\Expr\OrderByItem;
use SqlBuilder\Expr\OrWhere;
use SqlBuilder\Expr\OrWhereGroup;
use SqlBuilder\Expr\RightJoin;
use SqlBuilder\Expr\Select;
use SqlBuilder\Expr\SelectExpr;
use SqlBuilder\Expr\Union;
use SqlBuilder\Expr\UpdatePair;
use SqlBuilder\Expr\Table;
use SqlBuilder\Expr\UpdateExpr;
use SqlBuilder\Expr\Value;
use SqlBuilder\Expr\Where;
use SqlBuilder\Expr\WhereCondition;
use SqlBuilder\Expr\WhereGroup;
use function SqlBuilder\Expr\tap;

class Builder
{
    private $container = [
        'table',
        'from',
        'where',
        'values',
        'select',
        'groupBy',
        'having',
        'orderBy',
        'limit',
        'forUpdate',
        'updateSet',
        'union'
    ];
    protected $stack;
    protected $isInStack;
    private $connection;
    private $queryType;
    private $extraData;
    private $enableLog;
    private $queryLog;

    const SELECT = 1;
    const INSERT = 2;
    const UPDATE = 3;
    const DELETE = 4;

    public function __construct(MysqlConnection $connection)
    {
        $this->container = [
            'table' => new Table(),
            'where' => new WhereCondition(),
            'select' => new Select(),
            'groupBy' => new GroupBy(),
            'having' => new HavingCondition(),
            'orderBy' => new OrderBy(),
            'limit' => new Limit(),
            'forUpdate' => new ForUpdate(),
            'updateSet' => new UpdatePair(),
            'insertValue' => new InsertValue(),
            'union' => new Union()
        ];
        $this->stack = [];
        $this->isInStack = false;
        $this->extraData = [];
        $this->queryType = self::SELECT;
        $this->connection = $connection;
        $this->enableLog = false;
        $this->queryLog = [];
    }

    public function select(...$column)
    {
        foreach ($column as $it) {
            $this->container['select']->addItem(Value::make($it));
        }

        return $this;
    }
    public function selectRaw($sql, $bindValue = []) {
        return tap($this, function ($it) use ($sql, $bindValue) {
            foreach ($bindValue as $v) {
                $sql = preg_replace('/\?/', $v, $sql, 1);
            }
            $it->container['select']->addItem(Value::raw($sql));
        });
    }

    public function distinct() {
        return tap($this, function ($it) {
            $it->container['select']->setDistinct(true);
        });
    }

    public function count() {
        return tap($this, function ($it) {
            $it->container['select']->setAggregate(new Count());
        });
    }

    public function max($column) {
        return tap($this, function ($it) use ($column) {
            $it->container['select']->setAggregate(new Max($column));
        });
    }

    public function avg($column) {
        return tap($this, function ($it) use ($column) {
            $it->container['select']->setAggregate(new Avg($column));
        });
    }

    public function union(Builder $builder) {
        return tap($this, function($it) use ($builder) {
            $it->container['union']->addItem(Value::raw($builder));
        });
    }


    public function table(...$table) {
        return tap($this, function($it) use ($table) {
            $tb = new Table();
            foreach ($table as $t) {
                $tb->addItem(Value::make($t));
            }

            $it->container['table'] = $tb;
        });
    }

    public function from(...$table) {
        return $this->table(...$table);
    }

    public function where(...$where)
    {
        return tap($this, function($it) use ($where) {
            if (is_callable($where[0])) {
                $fn = $where[0];
                $this->isInStack = true;

                $fn($it);

                $whereGroup = new WhereGroup();
                foreach ($this->stack as $w) {
                    $whereGroup->addWhere($w);
                }

                $it->stack = [];
                $it->isInStack = false;

                $it->container['where']->addWhere($whereGroup);
            } else {
                $where = new Where($where[0], $where[1] ?? null, $where[2] ?? null);

                if ($it->isInStack) {
                    $it->stack[] = $where;

                } else {
                    $it->container['where']->addWhere($where);
                }
            }


        });
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

            $this->container['where']->addWhere($whereGroup);

            return $this;
        }

        $where = new OrWhere($where[0], $where[1] ?? null, $where[2] ?? null);

        if ($this->isInStack) {
            $this->stack[] = $where;

        } else {
            $this->container['where']->addWhere($where);
        }

        return $this;
    }

    public function having(...$where) {
        $where = new Having($where[0], $where[1] ?? null, $where[2] ?? null);

        if ($this->isInStack) {
            $this->stack[] = $where;
        } else {
            $this->container['having']->addWhere($where);
        }

        return $this;
    }

    public function get() {
       return $this->getQueryData(self::SELECT, [], true);
    }

    public function first()
    {
        return $this->getQueryData(self::SELECT, [], false);
    }

    public function update(array $data)
    {
        return $this->getQueryData(self::UPDATE, $data);
    }

    public function insert(array $data) {
        return $this->getQueryData(self::INSERT, $data);
    }

    public function delete() {
        return $this->getQueryData(self::DELETE);
    }
    
    public function join($table, $leftCol, $condition, $rightCol) {
        $this->container['table']->addJoin(new Join($table, $leftCol, $condition, $rightCol));
        return $this;
    }
    
    public function leftJoin($table, $leftCol, $condition, $rightCol) {
        $this->container['table']->addJoin(new LeftJoin($table, $leftCol, $condition, $rightCol));
        return $this;
    }
    
    public function rightJoin($table, $leftCol, $condition, $rightCol) {
        $this->container['table']->addJoin(new RightJoin($table, $leftCol, $condition, $rightCol));
        return $this;
    }
    
    


    public function limit($offset, $row = '') {
        $limit = new Limit();
        $limit->addItem(Value::raw($offset));

        if (!empty($row)) {
            $limit->addItem(Value::raw($row));
        }

        $this->container['limit'] = $limit;

        return $this;
    }

    public function forUpdate() {
        $this->container['forUpdate'] = new ForUpdate();
        return $this;
    }

    public function forShare() {
        $this->container['forUpdate'] = new ForShare();
        return $this;
    }

    public function orderBy($name, $direction = '') {
        $orderBy = new OrderByItem($name, $direction);

        $this->container['orderBy']->addItem(Value::make($orderBy));

        return $this;
    }


    public function enableQueryLog() {
        $this->enableLog = true;
    }

    public function getQueryLog() {
        return $this->queryLog;
    }

    private function getQueryData($type, array $extra = [], $multiple = true) {
        $timeStart = microtime(true);
        switch ($type) {
            case self::SELECT :
                [$sql, $bindValue] = $this->toSelect();
                $data = $this->connection->select($sql, $bindValue, $multiple);
                break;
            case self::INSERT :
                [$sql, $bindValue] = $this->toInsertSql($extra);
                $data = $this->connection->insert($sql, $bindValue);
                break;
            case self::UPDATE :
                [$sql, $bindValue] = $this->toUpdateSql($extra);
                $data = $this->connection->update($sql, $bindValue);
                break;
            case self::DELETE :
                [$sql, $bindValue] = $this->toDeleteSql();
                $data = $this->connection->delete($sql, $bindValue);
                break;
            default:
                throw new ExprException("Can't find this type: {$type}");
        }

        $this->queryLog($sql, $bindValue, (microtime(true) - $timeStart));

        return $data;
    }

    private function toSelect() {
        $expr = new SelectExpr(
            $this->container['select'],
            $this->container['table']->asFrom(),
            $this->container['where'],
            $this->container['groupBy'],
            $this->container['having'],
            $this->container['orderBy'],
            $this->container['limit'],
            $this->container['forUpdate'],
            $this->container['union'],

        );

        $result = $expr->compile();

        return $result;
    }

    private function queryLog($sql, $bindValue, $time = 0.0) {
        if (!$this->enableLog) {
            return;
        }
        $this->queryLog[] = [
            'query' => $sql,
            'bindValue' => $bindValue,
            'time' => $time
        ];
    }

    private function toInsertSql(array $data) {
        foreach ($data as $k => $v) {
            $this->container['insertValue']->addItem(Value::make([$k, $v]));
        }

        $expr = new InsertExpr($this->container['table'],
            $this->container['insertValue'],
        );

        return $expr->compile();

    }

    private function toUpdateSql(array $data) {
        foreach ($data as $k => $v) {
            $this->container['updateSet']->addItem(Value::make([$k, $v]));
        }

        $expr = new UpdateExpr($this->container['table'],
            $this->container['updateSet'],
            $this->container['where'],
            $this->container['orderBy'],
            $this->container['limit'],
        );

        return $expr->compile();

    }

    private function toDeleteSql() {
        $expr = new DeleteExpr($this->container['table'], $this->container['where']);
        return $expr->compile();
    }

    private function setQueryType($type, $extraData = []) {
        $this->queryType = $type;
        $this->extraData = $extraData;
    }


}
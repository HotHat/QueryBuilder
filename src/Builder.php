<?php declare(strict_types=1);


namespace QueryBuilder;


use Closure;
use Exception;
use QueryBuilder\Expr\Aggregate\Avg;
use QueryBuilder\Expr\Aggregate\Count;
use QueryBuilder\Expr\Aggregate\Max;
use QueryBuilder\Expr\DeleteExpr;
use QueryBuilder\Expr\ExprException;
use QueryBuilder\Expr\ForShare;
use QueryBuilder\Expr\ForLock;
use QueryBuilder\Expr\GroupBy;
use QueryBuilder\Expr\Having;
use QueryBuilder\Expr\HavingCondition;
use QueryBuilder\Expr\InsertExpr;
use QueryBuilder\Expr\InsertValue;
use QueryBuilder\Expr\Join;
use QueryBuilder\Expr\LeftJoin;
use QueryBuilder\Expr\Limit;
use QueryBuilder\Expr\OrderBy;
use QueryBuilder\Expr\OrderByItem;
use QueryBuilder\Expr\OrWhere;
use QueryBuilder\Expr\OrWhereGroup;
use QueryBuilder\Expr\RightJoin;
use QueryBuilder\Expr\Select;
use QueryBuilder\Expr\SelectExpr;
use QueryBuilder\Expr\Union;
use QueryBuilder\Expr\UpdatePair;
use QueryBuilder\Expr\Table;
use QueryBuilder\Expr\UpdateExpr;
use QueryBuilder\Expr\Value;
use QueryBuilder\Expr\Where;
use QueryBuilder\Expr\WhereCondition;
use QueryBuilder\Expr\WhereGroup;
use function QueryBuilder\Expr\tap;
use function QueryBuilder\Expr\with;

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
    private $stack;
    private $isInStack;
    private $connection;
    private $queryType;
    private $extraData;

    const SELECT = 1;
    const INSERT = 2;
    const UPDATE = 3;
    const DELETE = 4;

    public function __construct(MysqlConnection $connection)
    {
        $this->init();

        $this->stack = [];
        $this->isInStack = false;
        $this->extraData = [];
        $this->queryType = self::SELECT;
        $this->connection = $connection;
    }

    private function init() {
        $this->container = [
            'table' => new Table(),
            'where' => new WhereCondition(),
            'select' => new Select(),
            'groupBy' => new GroupBy(),
            'having' => new HavingCondition(),
            'orderBy' => new OrderBy(),
            'limit' => new Limit(),
            'forLock' => new ForLock(),
            'updateSet' => new UpdatePair(),
            'insertValue' => new InsertValue(),
            'union' => new Union()
        ];
    }

    private function reset() {
        $this->init();
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
        // tap($this, function ($it) {
        $this->container['select']->setAggregate(new Count());
        // });
        $data = $this->getQueryData(self::SELECT, [], false);

        // recover select
        $this->container['select']->setAggregate(null);

        return $data['count'];
    }

    public function max($column) {
        $this->container['select']->setAggregate(new Max($column));

        $data = $this->getQueryData(self::SELECT, [], false);

        // recover select
        $this->container['select']->setAggregate(null);

        return $data['max'];
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

    public function table(...$table) : Builder {
        return tap($this, function($it) use ($table) {
            $tb = new Table();
            foreach ($table as $t) {
                $tb->addItem(Value::make($t));
            }

            $it->container['table'] = $tb;
        });
    }

    public function from(...$table) : Builder {
        return $this->table(...$table);
    }

    public function where($first, $second, $third = null) : Builder {
        return tap($this, function($it) use ($first, $second, $third) {
            if (is_callable($first)) {
                $fn = $first;
                $it->isInStack = true;

                $fn($it);

                $whereGroup = new WhereGroup();
                foreach ($it->stack as $w) {
                    $whereGroup->addWhere($w);
                }

                $it->stack = [];
                $it->isInStack = false;

                $it->container['where']->addWhere($whereGroup);
            } else {
                $where = new Where($first, is_null($third) ? '=' : $second, $third ?? $second);

                if ($it->isInStack) {
                    $it->stack[] = $where;

                } else {
                    $it->container['where']->addWhere($where);
                }
            }
        });
    }

    public function orWhere($first, $second, $third) : Builder {
        return tap($this, function ($it) use ($first, $second, $third) {
            if (is_callable($first)) {
                $fn = $first;
                $it->isInStack = true;

                $fn($this);

                $whereGroup = new OrWhereGroup();
                foreach ($it->stack as $t) {
                    $whereGroup->addWhere($t);
                }

                $it->stack = [];
                $it->isInStack = false;

                $it->container['where']->addWhere($whereGroup);

            } else {
                $where = new OrWhere($first, is_null($third) ? '=' : $second, $third ?? $second);

                if ($it->isInStack) {
                    $it->stack[] = $where;
                } else {
                    $this->container['where']->addWhere($where);
                }
            }
        });
    }

    /*
     * whereIn / whereNotIn / orWhereIn / orWhereNotIn / whereNull / whereNotNull / orWhereNull / orWhereNotNull {{{
     */
    public function whereIn(string $column, array $data) {
        return $this->makeWhereInOrBetween($column, $data, 'IN');
    }

    public function whereNotIn(string $column, array $data) {
        return $this->makeWhereInOrBetween($column, $data, 'NOT IN');
    }

    public function orWhereIn(string $column, array $data) {
        return $this->makeWhereInOrBetween($column, $data, 'IN', false);
    }

    public function orWhereNotIn(string $column, array $data) {
        return $this->makeWhereInOrBetween($column, $data, 'NOT IN', false);
    }

    public function whereBetween(string $column, array $data) {
        return $this->makeWhereInOrBetween($column, $data, 'BETWEEN');
    }

    public function whereNotBetween(string $column, array $data) {
        return $this->makeWhereInOrBetween($column, $data, 'NOT BETWEEN');
    }

    public function orWhereBetween(string $column, array $data) {
        return $this->makeWhereInOrBetween($column, $data, 'BETWEEN', false);
    }

    public function orWhereNotBetween(string $column, array $data) {
        return $this->makeWhereInOrBetween($column, $data, 'NOT BETWEEN', false);
    }

    public function whereNull(string $column) {
        return $this->makeWhereNull($column, 'IS NULL');
    }

    public function whereNotNull(string $column) {
        return $this->makeWhereNull($column, 'IS NOT NULL');
    }

    public function orWhereNull(string $column) {
        return $this->makeWhereNull($column, 'IS NULL', false);
    }

    public function orWhereNotNull(string $column) {
        return $this->makeWhereNull($column, 'IS NOT NULL', false);
    }


    private function makeWhereInOrBetween(string $column, array $data, $op, bool $isAnd = true) : Builder {
        return tap($this, function($it) use ($column, $data, $op, $isAnd) {
            if ($isAnd) {
                $where = new Where($column, $op, $data);
            } else {
                $where = new OrWhere($column, $op, $data);
            }

            $it->container['where']->addWhere($where);
        });
    }

    private function makeWhereNull(string $column, $op, bool $isAnd = true) : Builder {
        return tap($this, function($it) use ($column, $op, $isAnd) {
            if ($isAnd) {
                $where = new Where($column, $op, []);
            } else {
                $where = new OrWhere($column, $op, []);
            }

            $it->container['where']->addWhere($where);
        });
    }

    /*
     * }}}
     */

    public function having(...$where) : Builder
    {
        $where = new Having($where[0], $where[1] ?? null, $where[2] ?? null);

        if ($this->isInStack) {
            $this->stack[] = $where;
        } else {
            $this->container['having']->addWhere($where);
        }

        return $this;
    }

    
    public function join($table, $leftCol, $condition, $rightCol) : Builder {
        $this->container['table']->addJoin(new Join($table, $leftCol, $condition, $rightCol));
        return $this;
    }
    
    public function leftJoin($table, $leftCol, $condition, $rightCol) : Builder {
        $this->container['table']->addJoin(new LeftJoin($table, $leftCol, $condition, $rightCol));
        return $this;
    }
    
    public function rightJoin($table, $leftCol, $condition, $rightCol) {
        $this->container['table']->addJoin(new RightJoin($table, $leftCol, $condition, $rightCol));
        return $this;
    }
    
    public function limit($offset, $row = '')  : Builder{
        return tap($this, function ($it) use ($offset, $row) {
            $limit = new Limit();
            $limit->addItem(Value::raw($offset));

            if (!empty($row)) {
                $limit->addItem(Value::raw($row));
            }

            $it->container['limit'] = $limit;
        });
    }

    public function forUpdate()  : Builder{
        return tap($this, function ($it) {
            $it->container['forLock']->setType(ForLock::UPDATE);
        });
    }

    public function forShare() : Builder {
        return tap($this, function ($it) {
            $it->container['forLock']->setType(ForLock::SHARE);
        });
    }

    public function orderBy($name, $direction = '') : Builder {
        return tap($this, function ($it) use ($name, $direction){
            $orderBy = new OrderByItem($name, $direction);
            $it->container['orderBy']->addItem(Value::make($orderBy));
        });
    }

    public function get() {
        $data = $this->getQueryData(self::SELECT, [], true);
        $this->reset();
        return $data;
    }

    public function first() {
        $data = $this->getQueryData(self::SELECT, [], false);
        $this->reset();
        return $data;
    }

    public function update(array $data) {
        return $this->getQueryData(self::UPDATE, $data);
    }

    public function insert(array $data) {
        return $this->getQueryData(self::INSERT, $data);
    }

    public function transaction(Closure $func) {
        $this->connection->transaction();

        try {

            $func();

            $this->connection->commit();

        } catch (Exception $e) {
            $this->connection->rollBack();
            throw $e;
        }
    }

    public function delete() {
        return $this->getQueryData(self::DELETE);
    }

    public function enableQueryLog() {
        $this->connection->enableQueryLog();
    }

    public function getQueryLog() {
        return $this->connection->getQueryLog();
    }

    private function getQueryData($type, array $extra = [], $multiple = true) {
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
            $this->container['forLock'],
            $this->container['union']

        );

        $result = $expr->compile();

        // $this->reset();

        return $result;
    }

    private function toInsertSql(array $data) {
        foreach ($data as $k => $v) {
            $this->container['insertValue']->addItem(Value::make([$k, $v]));
        }

        $expr = new InsertExpr($this->container['table'],
            $this->container['insertValue']
        );
        $this->init();
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
            $this->container['limit']
        );
        $this->init();
        return $expr->compile();
    }

    private function toDeleteSql() {
        $expr = new DeleteExpr($this->container['table'], $this->container['where']);
        $this->init();
        return $expr->compile();
    }
}
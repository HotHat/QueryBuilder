<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


use SqlBuilder\Expr\aggregate\Aggregate;

class Select extends Column
{
    protected $tag = 'SELECT';

    private $aggregate;
    private $distinct;

    public function __construct()
    {
        $this->aggregate = null;
        $this->distinct = false;

        parent::__construct();
    }

    public function setAggregate(Aggregate $aggregate) {
        $this->aggregate = $aggregate;
    }

    public function setDistinct(bool $need) {
        $this->distinct = $need;
    }

    public function compile() : string
    {
        if ($this->aggregate != null) {
            return sprintf('SELECT %s', $this->aggregate->compile());
        }

        if ($this->isEmpty()){
            $this->addItem(Value::raw('*'));
        }

        if ($this->distinct) {
            $sql = parent::compile();
            $sql = str_replace($this->tag, $this->tag . ' DISTINCT', $sql);
            return $sql;
        }
        return parent::compile();
    }

}
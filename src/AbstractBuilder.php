<?php declare(strict_types=1);


namespace ZiWen\SqlBuilder;


abstract class AbstractBuilder
{
    protected $tableArr;
    protected $columnArr;
    protected $whereArr;
    protected $orderByArr;
    protected $havingArr;
    protected $groupByArr;
    protected $limitArr;
    protected $bForUpdate;
    protected $bLock;
    protected $bindValue;

    public function __construct()
    {
        $this->tableArr = [];
        $this->columnArr = [];
        $this->whereArr = [];
        $this->orderByArr = [];
        $this->havingArr = [];
        $this->groupByArr = [];
        $this->limitArr = [];
        $this->bForUpdate = false;
        $this->bLock = false;
        $this->bindValue = [];
    }

}
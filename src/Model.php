<?php declare(strict_types=1);


namespace SqlBuilder;


use function SqlBuilder\Expr\wrapValue;

class Model
{
    protected $table;
    protected $connection;


    private $builder;

    public function __construct()
    {
        $this->connection = getDefaultConnection();
        $this->builder = new Builder($this->connection);

    }

    public function enableQueryLog() {
        $this->connection->enableQueryLog();
    }

    public function getQueryLog() {
        return $this->connection->getQueryLog();
    }

    public function getTable() : string {
        return $this->table;
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->{$name}(...$arguments);
        }
        $this->builder->table($this->table);
        // if ($name == 'select') {
        //     $arguments = array_map(function ($it) {
        //         return sprintf('%s.%s', $this->getTable(), $it);
        //     }, $arguments);
        // }
        return $this->builder->{$name}(...$arguments);
    }

    public static function __callStatic($name, $arguments)
    {
        if (method_exists(get_called_class(), $name)) {
            return self::{$name}(...$arguments);
        }

        return (new static())->{$name}(...$arguments);
    }

}
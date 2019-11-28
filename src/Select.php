<?php declare(strict_types=1);

namespace ZiWen\SqlBuilder;
use ZiWen\SqlBuilder\scheme\From;
use ZiWen\SqlBuilder\scheme\Parse;

class Select implements Parse
{
    private $container;

    public function __construct($container = [])
    {
        $this->container = $container;
    }

    public function table(...$table) : Select {
        $from = new From();
        foreach ($table as $it) {
            $from->addItem($it);
        }

        $this->container[] = $from;

        return $this;
    }

    public function from(...$table) : Select {
        return $this->table(...$table);
    }


    public function orderBy()
    {

    }

    public function having()
    {

    }

    public function where()
    {

    }
    public function orWhere()
    {

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


    public function compile()
    {

    }
}
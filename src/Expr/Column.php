<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class Column implements Parse
{
    protected $container;
    protected $tag;
    protected $escape = true;


    public function __construct()
    {
        $this->container = [];
    }

    public function addItem($it) {
        array_push($this->container, $it);
    }

    public function escapeCode() : string
    {
       return $this->escape ? '`' : '';
    }

    public function isEmpty() {
       return empty($this->container);
    }

    public function emptyHandle($func) {
        return compileWithDefault($this->isEmpty(), $func, '');
    }

    public function compile() : string {
        return $this->emptyHandle(function() {
            $column = array_map(function ($it) {
                return wrapValue($it);
            }, $this->container);

            return sprintf(' %s %s', $this->tag, implode(', ', $column));
        });

    }

}
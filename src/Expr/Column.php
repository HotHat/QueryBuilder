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
        if ($this->isEmpty()){
            return '';
        }

        return $func();
    }

    public function compile() : string {
        return $this->emptyHandle(function() {
            $column = array_map(function ($it) {
                $s = explode('.', (string)$it);

                $e = array_map(function ($it) {
                    return sprintf('%s%s%s', $this->escapeCode(), $it, $this->escapeCode());
                }, $s);

                return implode('.', $e);

            }, $this->container);

            return sprintf(' %s %s', $this->tag, implode(', ', $column));
        });

    }

}
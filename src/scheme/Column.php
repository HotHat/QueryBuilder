<?php declare(strict_types=1);


namespace SqlBuilder\scheme;


class Column implements Parse
{
    protected $contain;
    protected $tag;
    protected $escape = true;


    public function __construct()
    {
        $this->contain = [];
    }

    public function addItem($it) {
        array_push($this->contain, $it);
    }

    public function escapeCode() : string
    {
       return $this->escape ? '`' : '';
    }

    public function compile() : string {
        $column = array_map(function ($it) {
            $s = explode('.', $it);

            $e = array_map(function ($it) {
                return sprintf('%s%s%s', $this->escapeCode(), $it, $this->escapeCode());
            }, $s);

            return implode('.', $e);

        }, $this->contain);

        return sprintf('%s %s', $this->tag, implode(', ', $column));
    }

}
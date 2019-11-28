<?php declare(strict_types=1);


namespace SqlBuilder\scheme;


class Set extends Column
{
    protected $tag = 'SET';

    public function compile(): string
    {
        $this->contain = array_map(function ($it) {
            return sprintf("%s%s%s='%s'", $this->escapeCode(), $it[0], $this->escapeCode(), $it[1]);
        }, $this->contain);

        $this->escape = false;

        return parent::compile();
    }

}
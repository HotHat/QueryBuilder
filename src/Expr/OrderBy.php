<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class OrderBy extends Column
{
    protected $tag = 'ORDER BY';

    public function compile(): string
    {
        $escapeKeep = $this->escape;
        $this->contain = array_map(function ($it)  {

            if (is_array($it)) {
                return sprintf('%s%s%s %s', $this->escapeCode(), $it[0], $this->escapeCode(), $it[1]);
            } else {
                return sprintf('%s', $it);
            }

        }, $this->contain);
        $this->escape = false;

        $sql = parent::compile();
        $this->escape = $escapeKeep;

        return $sql;
    }
}
<?php declare(strict_types=1);


namespace SqlBuilder\Expr;

use function SqlBuilder\Expr\prefixSpace;

class OrderBy extends Column
{
    protected $tag = 'ORDER BY';

    public function compile(): string
    {
        if(empty($this->contain)) {
            return '';
        }

        $lst = array_map(function (orderByItem $it) {
            return sprintf('%s%s%s%s', $this->escapeCode(), $it->column, $this->escapeCode(),
                empty($it->direction) ? '' : prefixSpace($it->direction));

        }, $this->contain);

        return sprintf('%s %s', $this->tag, implode(', ', $lst));
    }
}
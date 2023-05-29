<?php declare(strict_types=1);


namespace QueryBuilder\Expr;


class OrderBy extends Column
{
    protected $tag = 'ORDER BY';

    public function compile(): string
    {
        if(empty($this->container)) {
            return '';
        }

        $lst = array_map(function (Value $it) {
            // $item =
            return $it->toString(function (Value $it) {
                $item = $it->getValue();
                return sprintf('%s%s', wrapValue($item->column),
                    empty($item->direction) ? '' : prefixSpace($item->direction));
            });

        }, $this->container);

        return sprintf(' %s %s', $this->tag, implode(', ', $lst));
    }
}
<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class WhereCondition implements Parse
{
    protected $contain;


    /**
     * WhereItem | WhereCondition
     * @param $item
     */
    public function addWhere(Conjunct $item) {
        $this->contain[] = $item;
    }

    public function compile() : array
    {
        if (empty($this->contain)) {
            return ['', []];
        }

        $first = true;

        $bindValue = [];

        $list = array_map(function (Conjunct $it) use (&$first, &$bindValue) {

            if ($first) {
                $prefix = '';
                $first = false;
            } else {
                $prefix = ($it->isAnd() ? 'AND' : 'OR') . ' ';
            }

            [$sql, $value] = $it->compile();

            if (!empty($value)) {
                if (is_array($value)) {
                    $bindValue = array_merge($bindValue, $value);
                } else {
                    $bindValue[] = $value;
                }
            }

            return sprintf('%s%s', $prefix, $sql);

        }, $this->contain);

        return [sprintf('%s', implode(' ', $list)), $bindValue];
    }


}
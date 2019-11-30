<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class WhereCondition implements Parse
{
    protected $container;
    protected $tag = 'WHERE';


    /**
     * WhereItem | WhereCondition
     * @param $item
     */
    public function addWhere(Conjunct $item) {
        $this->container[] = $item;
    }
    
    public function isEmpty() {
        return empty($this->container);
    }

    public function compile() : array
    {
        return compileWithDefault($this->isEmpty(), function () {
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
        
            }, $this->container);
    
            return [prefixSpace(sprintf('%s %s', $this->tag, implode(' ', $list))), $bindValue];
        }, ['', []]);

        
    }


}
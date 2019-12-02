<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class Table extends Column
{
    protected $tag = '';
    protected $join = [];
    
    
    public function addTable ($table) {
        $this->addItem($table);
    }
    
    public function addJoin($join) {
        $this->join[] = $join;
    }
    
   public function asTable() {
       $this->tag = '';
       return $this;
   }
    
    public function asFrom() {
        $this->tag = 'FROM';
        return $this;
    }
    
    public function compile(): string
    {
        
        $join = $this->join;
        
        $l = compileWithDefault(empty($join), function () use ($join) {
            $s = array_map(function (Join $it) {
                return $it->compile();
            }, $this->join);
            
            return implode(' ', $s);
        });
        
        return sprintf(' %s%s', trim(parent::compile()), prefixSpace($l));
    }
}
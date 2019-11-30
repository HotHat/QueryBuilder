<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class Join implements Parse
{
    protected $tag = 'JOIN';
    protected $leftCol;
    protected $rightCol;
    protected $table;
    private $condition;
    
    
    public function __construct($table, $leftCol, $condition,  $rightCol)
    {
        $this->leftCol = $leftCol;
        $this->rightCol = $rightCol;
        $this->table = $table;
        $this->condition = $condition;
    }
    
    public function compile()
    {
        return sprintf('%s %s ON %s%s%s',
            $this->tag,
            wrapValue($this->table),
            wrapValue($this->leftCol),
            $this->condition,
            wrapValue($this->rightCol)
        );
    }
}
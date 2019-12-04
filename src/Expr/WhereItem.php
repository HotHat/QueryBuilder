<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


abstract class WhereItem implements Conjunct
{
    private $first;
    private $second;
    private $third;

    const WHERE_OP = [
        '=', '>', '>', '<>', '!=', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN', 'IS NULL', 'IS NOT NULL'
    ];


    public function __construct($first, $second, $third)
    {
        $this->first  = $first;
        $this->second = $second;
        $this->third  = $third;

    }

    abstract public function isAnd() : bool;

    public function compile() : array {
        return $this->passThree($this->first, $this->second, $this->third);
    }


    private function passThree($f, $s, $t) : array {
        if (!in_array($s, self::WHERE_OP)) {
            throw new ExprException(sprintf('%s: Where operator not allow', $s));
        }

        if (in_array(strtoupper($s), ['IN', 'NOT IN'])) {
            $placeholder = array_map(function ($it) { return '?';}, $t);
            $mark = sprintf('(%s)', implode(', ', $placeholder));
            return [sprintf("%s %s %s", wrapValue($f), $s, $mark), $t];
        } else if (in_array(strtoupper($s), ['BETWEEN', 'NOT BETWEEN'])) {
            if (!is_array($t) || count($t) != 2) {
                throw new ExprException(sprintf('%s must with array of two element', $s));
            }

            $mark = sprintf('%s AND %s', $t[0], $t[1]);
            return [sprintf("%s %s %s", wrapValue($f), $s, $mark), $t];

        } else if (in_array(strtoupper($s), ['IS NULL', 'IS NOT NULL'])) {
            return [sprintf("%s %s", wrapValue($f), $s), []];
        }

        return [sprintf("%s%s%s", wrapValue($f), $s, '?'), $t];
    }

}
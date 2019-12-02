<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


abstract class WhereItem implements Conjunct
{

    private $number;
    private $first;
    private $second;
    private $third;
    private $bindValue;


    public function __construct($first, $second = null, $third = null)
    {
        $this->first  = $first;
        $this->second = $second;
        $this->third  = $third;
        $this->number = 1;

        if (!empty($third)) {
            $this->number = 3;
            $this->bindValue = $third;
        } else if (!empty($second)) {
            $this->number = 2;
            $this->bindValue = $second;
        }
    }

    abstract public function isAnd() : bool;

    public function compile() : array {

        if ($this->number == 1) {
            return $this->pass($this->first);
        }

        if ($this->number == 2) {
            return $this->passTwo($this->first, $this->second);
        }

        if ($this->number == 3) {
            return $this->passThree($this->first, $this->second, $this->third);
        }
    }

    private function pass($f) : array {
        return [$f, []];
    }

    private function passTwo($f, $s) : array {
        return [sprintf("`%s`=%s", $f, '?'), $s];
    }

    private function passThree($f, $s, $t) : array {
        return [sprintf("`%s`%s%s", $f, $s, '?'), $t];
    }

}
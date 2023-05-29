<?php declare(strict_types=1);


namespace QueryBuilder\Expr;


use Closure;

class Value
{
    /**
     * @var string
     */
    private $value;
    /**
     * @var bool
     */
    private $isRaw;

    public function __construct($value , bool $isRaw = false)
    {
        $this->value = $value;
        $this->isRaw = $isRaw;
    }

    public function isRaw() {
        return $this->isRaw;
    }

    public function getValue() {
        return $this->value;
    }

    public function toString(Closure $func) {
        return $func($this);
    }

    public static function make($value) {
        return new static($value);
    }

    public static function raw($value) {
        return new static($value, true);
    }

}
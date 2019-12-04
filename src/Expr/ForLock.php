<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class ForLock implements CompileToString
{
    private $lockFor;
    const NONE   = 1;
    const SHARE  = 2;
    const UPDATE = 3;

    public function __construct()
    {
        $this->lockFor = self::NONE;
    }

    public function setType($type) {
        $this->lockFor = $type;
    }

    public function compile(): string
    {
        switch ($this->lockFor) {
            case self::NONE:
                return '';
                break;
            case self::SHARE:
                return ' FOR SHARE';
                break;
            case self::UPDATE:
                return ' FOR UPDATE';
                break;
            default:
                throw new ExprException('Not this lock type: ' . $this->lockFor);
        }
    }

}
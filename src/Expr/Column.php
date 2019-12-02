<?php declare(strict_types=1);


namespace SqlBuilder\Expr;


class Column
{
    protected $container;
    protected $tag;

    public function __construct()
    {
        $this->container = [];
    }

    public function addItem(Value $it) {
        array_push($this->container, $it);
    }

    public function addFront(Value $it) {
        array_unshift($this->container, $it);
    }


    public function isEmpty() {
       return empty($this->container);
    }

    public function getContainer() {
        return $this->container;
    }

    public function getTag() {
        return $this->tag;
    }

    // public function compile() : string {
    //     return compileWithDefault($this->isEmpty(), function () {
    //         $column = array_map(function (Value $it) {
    //             return $it->toString(function ($it) {
    //                 if ($it->isRaw()) {
    //                     return $it->getValue();
    //                 } else {
    //                     return wrapValue($it->getValue());
    //                 }
    //             });
    //         }, $this->container);
    //
    //         return sprintf(' %s %s', $this->tag, implode(', ', $column));
    //     });
    // }

}
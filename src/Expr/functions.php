<?php declare(strict_types=1);


namespace SqlBuilder\Expr;

function prefixSpace($str) {
    return sprintf(' %s', $str);
}

function wrapValue($value) {
    return sprintf('`%s`', $$value);
}

function compileWithDefault($test, $func, $default = '') {
    if ($test){
        return $default;
    }
    
    return $func();
}

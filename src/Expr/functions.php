<?php declare(strict_types=1);


namespace SqlBuilder\Expr;

function prefixSpace($str) {
    return sprintf(' %s', $str);
}

function wrapValue($value) {
    $s = explode('.', (string)$value);
    $l = array_map(function ($it) {
        return sprintf('`%s`', $it);
    }, $s);
    return implode('.', $l);
}

function compileWithDefault($test, $func, $default = '') {
    if ($test){
        return $default;
    }
    
    return $func();
}

<?php declare(strict_types=1);


namespace QueryBuilder\Expr;

use Closure;

function prefixSpace($str) {
    return empty($str) ? '' :sprintf(' %s', $str);
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

function compileToString(Column $container) : string {
    return compileWithDefault($container->isEmpty(), function () use ($container) {
        $column = array_map(function (Value $it) {
            return $it->toString(function ($it) {
                if ($it->isRaw()) {
                    return $it->getValue();
                } else {
                    return wrapValue($it->getValue());
                }
            });
        }, $container->getContainer());

        return sprintf(' %s %s', $container->getTag(), implode(', ', $column));
    });
}

function tap($it, Closure $func = null) {
    if (isset($func)) {
        $func($it);
    }
    return $it;
}

function with($it, Closure $func = null) {
    if (isset($func)) {
        return $func($it);
    }
    return $it;
}


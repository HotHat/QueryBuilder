<?php declare(strict_types=1);


namespace QueryBuilder;


use Closure;
use Exception;

class BuilderStatic
{
    private static $connection;
    private static $builder;

    public static function setConnection($connection) {
        self::$connection = $connection;
        self::$builder = new Builder(self::$connection);
    }

    public static function __callStatic($name, $arguments)
    {
        if (method_exists(get_called_class(), $name)) {
            return self::{$name}(...$arguments);
        }

        return self::$builder->{$name}(...$arguments);
    }

    public static function enableQueryLog() {
        self::$builder->enableQueryLog();
    }

    public static function getQueryLog() {
        return self::$builder->getQueryLog();
    }


    public static function transaction(Closure $func) {
        self::$connection->transaction();

        try {

            $func();

            self::$connection->commit();

        } catch (Exception $e) {
            self::$connection->rollBack();
            throw $e;
        }
    }

}
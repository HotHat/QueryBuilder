<?php declare(strict_types=1);

namespace SqlBuilder;

function getDefaultConnection() {
    $host = '192.168.68.8';
    $port = 3306;
    $dbname = 'ziwen';
    $user = 'homestead';
    $password = 'secret';

    return new MysqlConnection($host, $port, $dbname, $user, $password);
}
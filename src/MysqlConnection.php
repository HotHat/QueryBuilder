<?php
namespace SqlBuilder;

use Closure;
use PDO;
use PDOException;
use PDOStatement;

class MysqlConnection
{
    private $pdo;

    private $fetchMode;

    private $rowCount;

    public function __construct($host, $port, $database, $user, $password, $charset = 'utf8')
    {
        $this->fetchMode = PDO::FETCH_ASSOC;
        $this->rowCount = 0;

        $this->connect($host, $port, $database, $user, $password, $charset);
    }

    public function connect($host, $port, $database, $user, $password, $charset = 'utf8') {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $host, $port, $database, $charset);
        $this->pdo = new PDO($dsn, $user, $password);
        $attr = [
            PDO::ATTR_PERSISTENT => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . $charset,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        foreach ($attr as $k => $v) {
            $this->pdo->setAttribute($k, $v);
        }
    }

    public function update(string $sql, array $params) {
        return $this->query($sql, $params, function ($pdo, $stmt) {
            return true;
        }, false);

    }

    public function select(string $sql, array $params, $multiple = false) {
        return $this->query($sql, $params, function ($pdo, $stmt) use ($multiple) {
            $stmt->setFetchMode($this->fetchMode);
            if ($multiple) {
                return $stmt->fetchAll();
            }
            return $stmt->fetch();
        }, null);
    }

    public function insert(string $sql, array $params) {
        return $this->query($sql, $params, function ($pdo,$stmt) {
            return $pdo->lastInsertId();
        }, false);
    }

    public function delete(string $sql, array $params) {
        return $this->query($sql, $params, function ($pdo, $stmt) {
            return true;
        }, false);
    }

    public function transaction() {
        $start = $this->pdo->beginTransaction();

        if (!$start) {
            throw new PDOException('Transaction init failure');
        }
    }

    public function rollBack() {
        $this->pdo->rollBack();
    }

    public function commit() {

        $this->pdo->commit();
    }

    private function query(string $sql, array $params, Closure $func, $failValue) {

        $stmt = $this->pdo->prepare($sql);

        if ($stmt->execute($params)) {
            // select statement

            return $func($this->pdo, $stmt);

        } else {
            $error = $stmt->errorInfo();

            if ($error[0] != '00000') {
                throw new PDOException(sprintf('Error Code: %s; Driver Code: %s; Info: %s', $error[0], $error[1], $error[2]));
            }

            return $failValue;
        }
    }

}
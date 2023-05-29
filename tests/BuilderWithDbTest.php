<?php declare(strict_types=1);


use PHPUnit\Framework\TestCase;

class BuilderWithDbTest extends TestCase
{

    private $builder;
    private $host;
    private $port;
    private $dbname;
    private $user;
    private $password;

    public function setUp() : void {
        $this->host = '192.168.68.8';
        $this->port = 3306;
        $this->dbname = 'ziwen';
        $this->user = 'homestead';
        $this->password = 'secret';

        $connection = new \QueryBuilder\MysqlConnection($this->host, $this->port, $this->dbname, $this->user, $this->password);

        $this->builder = new \QueryBuilder\Builder($connection);
        $this->builder->enableQueryLog();
    }

    public function tearDown() :void {
        $log = $this->builder->getQueryLog();
        var_dump($log);
    }

    public function testGet() {
        $sql = $this->builder->table('user')->get();

        var_dump($sql);

        $sql = $this->builder->table('user')->where('id', 1)->get();

        var_dump($sql);

        $sql = $this->builder->table('user')->where('id', 1)->first();

        var_dump($sql);

    }
    public function testLimit() {

        $sql = $this->builder->table('user')->limit(2, 3)->get();

        var_dump($sql);
    }

    public function testInset() {
        $id = $this->builder->table('user')->insert([
            'name' => 'sql',
            'age' => 3
        ]);

        var_dump($id);
    }

    public function testUpdate() {
        $data = $this->builder->table('user')->where('id', 6)->update([
            'name' => 'update test',
        ]);

        var_dump($data);
    }

    public function testDelete() {
        $data = $this->builder->table('user')->where('id', 6)->delete();

        var_dump($data);
    }

    public function testForUpdate() {
        $data = $this->builder->table('user')->where('id', 6)->forUpdate()->get();
        var_dump($data);
    }

    public function testTransaction() {
        $this->builder->transaction(function () {
            $this->builder->table('user')->insert([
                'name' => 'transaction test',
                'age' => 12
            ]);

            $data = $this->builder->table('user')->where('id', 4)->forUpdate()->first();

            var_dump($data);

            $this->builder->table('user')->where('id', 5)->update([
                'name' => 'well done'
            ]);
            throw new \QueryBuilder\Expr\ExprException('Transaction Error Test!');

        });

    }

}
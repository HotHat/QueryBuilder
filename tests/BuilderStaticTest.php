<?php declare(strict_types=1);


use PHPUnit\Framework\TestCase;

use SqlBuilder\BuilderStatic as DB;

class BuilderStaticTest extends TestCase
{

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

        $connection = new \SqlBuilder\MysqlConnection($this->host, $this->port, $this->dbname, $this->user, $this->password);

        // $this->builder = new \SqlBuilder\Builder($connection);
        // $this->builder->enableQueryLog();
        DB::setConnection($connection);
        DB::enableQueryLog();
    }

    public function tearDown() :void {
        $log = DB::getQueryLog();
        var_dump($log);
    }

    public function testGet() {
        $sql = DB::table('user')->get();

        var_dump($sql);

        $sql = DB::table('user')->where('id', 1)->get();

        var_dump($sql);

        $sql = DB::table('user')->where('id', 1)->first();

        var_dump($sql);

    }
    public function testLimit() {

        $sql = DB::table('user')->limit(2, 3)->get();

        var_dump($sql);
    }

    public function testInset() {
        $id = DB::table('user')->insert([
            'name' => 'Builder Static Test',
            'age' => 3
        ]);

        var_dump($id);
    }

    public function testUpdate() {
        $data = DB::table('user')->where('id', 6)->update([
            'name' => 'Builder Static update test',
        ]);

        var_dump($data);
    }

    public function testDelete() {
        $data = DB::table('user')->where('id', 6)->delete();

        var_dump($data);
    }

    public function testForUpdate() {
        $data = DB::table('user')->where('id', 6)->forUpdate()->get();
        var_dump($data);
    }

    public function testTransaction() {
        DB::transaction(function () {
            DB::table('user')->insert([
                'name' => 'builder static transaction test',
                'age' => 12
            ]);

            $data = DB::table('user')->where('id', 4)->forUpdate()->first();

            var_dump($data);

            DB::table('user')->where('id', 5)->update([
                'name' => 'builder static transaction well done'
            ]);

            throw new \SqlBuilder\Expr\ExprException('Transaction Error Test!');

        });

    }

}
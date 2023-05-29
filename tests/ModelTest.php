<?php declare(strict_types=1);


use PHPUnit\Framework\TestCase;

use Tests\UserModel;

class ModelTest extends TestCase
{

    private $model;

    public function setUp() : void {

        $this->model = new \Tests\UserModel();
        $this->model->enableQueryLog();
    }

    public function tearDown() :void {
        $log = $this->model->getQueryLog();
        var_dump($log);
    }

    public function testGet() {
        $sql = $this->model->get();

        var_dump($sql);

        $sql = $this->model->where('id', 1)->get();

        var_dump($sql);

        $sql = $this->model->where('id', 1)->first();

        var_dump($sql);

        $sql = $this->model->select('id', 'name', 'age')->where('id', 1)->first();

        var_dump($sql);

    }

    public function testStaticGet() {
        $sql = UserModel::get();

        var_dump($sql);

        $sql = UserModel::where('id', 1)->get();

        var_dump($sql);

        $sql = UserModel::where('id', 1)->first();

        var_dump($sql);

        $sql = UserModel::select('id', 'name', 'age')->where('id', 1)->first();

        var_dump($sql);

    }

    public function testWhere() {

       $data = DB::table('user')->where('id', 1)->get();
       var_dump($data);
    }

    public function testWhereIn() {
        $data = DB::table('user')
            ->whereIn('id', [1, 2, 3])
            ->get();
        var_dump($data);

        $data = DB::table('user')
            ->whereNotIn('id', [1, 2, 3])
            ->get();

        var_dump($data);
    }

    public function testNull() {
        $data = DB::table('user')
            ->whereNull('id')
            ->get();
        var_dump($data);

        $data = DB::table('user')
            ->whereNotNull('id')
            ->get();

        var_dump($data);
    }

    public function testWhereBetween() {
        $data = DB::table('user')
            ->whereBetween('id', [1, 3])
            ->get();
        var_dump($data);

        $data = DB::table('user')
            ->whereNotBetween('id', [1, 10])
            ->get();

        var_dump($data);
    }

    public function testLimit() {

        $sql = DB::table('user')->limit(2, 3)->get();

        var_dump($sql);
    }

    public function testInsert() {
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

            throw new \QueryBuilder\Expr\ExprException('Transaction Error Test!');

        });

    }

}
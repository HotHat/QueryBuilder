<?php declare(strict_types=1);


use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{

    private $builder;
    public function setUp() : void {
        $this->builder = new \SqlBuilder\Builder();
    }

    public function testGet() {

        $sql = $this->builder->table('users')->where('id', 1)->get();

        var_dump($sql);
    }


    public function testSelect() {
        $builder = new \SqlBuilder\Builder();
        $sql = $builder->table('users')->select('id', 'name')->where('id', 1)->get();

        var_dump($sql);
    }

    public function testSelectRaw() {
        $sql = $this->builder->table('users')->selectRaw('price * ?', [123])->where('id', 1)->get();

        var_dump($sql);
    }

    public function testOrderBy() {
        $sql = $this->builder->table('users')
            ->select('id', 'name')
            ->where('id', 1)
            ->orderBy('id', 'DESC')
            ->orderBy('name', 'ASC')
            ->get();
        var_dump($sql);
    }

    public function testLimit() {
        $builder = new \SqlBuilder\Builder();
        $sql = $builder->table('users')->select('id', 'name')->where('id', 1)->limit(1)->get();
        var_dump($sql);

        $sql = $builder->table('users')->select('id', 'name')->where('id', 1)->limit(1, 2)->get();
        var_dump($sql);
    }

    public function testForUpdate() {
        $builder = new \SqlBuilder\Builder();
        $sql = $builder->table('users')->select('id', 'name')
            ->forUpdate()
            ->where('id', 1)->get();

        var_dump($sql);
    }
    public function testForShare() {
        $builder = new \SqlBuilder\Builder();
        $sql = $builder->table('users')->select('id', 'name')
            ->forShare()
            ->where('id', 1)->get();

        var_dump($sql);
    }
    
    public function testWhere() {
        $builder = new \SqlBuilder\Builder();
        $sql = $builder->table('users')->select('id', 'name')
            ->where('id', 1)->get();
        
        var_dump($sql);
    }
    
    public function testHaving() {
        $builder = new \SqlBuilder\Builder();
        $sql = $builder->table('users')->select('id', 'name')
            ->having('id', 1)->get();

        var_dump($sql);
    }
    
    
    public function testTableJoin() {
        $builder = new \SqlBuilder\Builder();
        $sql = $builder->table('users')->select('id', 'name')
            ->join('orders', 'users.id', '=', 'orders.user_id')->get();
        
        var_dump($sql);
    }


    public function testUpdate() {
        $r = $this->builder->table('users')
            ->where('id', 1)
            ->update([
                'votes' => 1,
                'name' => 'hello world',
                'age' => 12,
            ]);

        var_dump($r);
    }

    public function testDelete() {
        $r = $this->builder->table('users')
            ->where('id', 1)
            ->delete();

        var_dump($r);
    }

    public function testInsert() {
        $r = $this->builder->table('user')
            ->insert([
                'id' => 1,
                'name' => 'hello world',
                'age' => 12,
            ]);

        var_dump($r);
    }

    public function testCount() {
        $r = $this->builder->table('user')
            ->count()
            ->get();

        var_dump($r);
    }

    public function testDistinct() {
        $r = $this->builder->table('user')
            ->distinct()
            ->get();

        var_dump($r);
    }

    public function testMax() {
        $r = $this->builder->table('user')
            ->max('id')
            ->get();

        var_dump($r);
    }

    public function testAvg() {
        $r = $this->builder->table('users')
            ->avg('id')
            ->get();

        var_dump($r);
    }




}
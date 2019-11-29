<?php declare(strict_types=1);


use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{

    public function testGet() {

        $builder = new \SqlBuilder\Builder();
        $sql = $builder->table('users')->where('id', 1)->get();

        var_dump($sql);
    }


    public function testSelect() {
        $builder = new \SqlBuilder\Builder();
        $sql = $builder->table('users')->select('id', 'name')->where('id', 1)->get();

        var_dump($sql);
    }

    public function testOrderBy() {
        $builder = new \SqlBuilder\Builder();
        // $sql = $builder->table('users')->select('id', 'name')->where('id', 1)->orderBy('id')->get();
        // var_dump($sql);
        // $sql = $builder->table('users')->select('id', 'name')->where('id', 1)->orderBy('id', 'ASC')->get();
        // var_dump($sql);
        $sql = $builder->table('users')
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

    public function testHaving() {
        $builder = new \SqlBuilder\Builder();
        $sql = $builder->table('users')->select('id', 'name')
            ->having('id', 1)->get();

        var_dump($sql);
    }


}
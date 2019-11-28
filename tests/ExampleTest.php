<?php



use PHPUnit\Framework\TestCase;
use SqlBuilder\SelectCompile;
use SqlBuilder\Where;
use SqlBuilder\Builder;

class ExampleTest extends  TestCase
{
    public function testHello() {
        echo "Hello World";
        $this->assertTrue(true);
    }

    public function testBuilder1() {
        $builder = new Builder();

        $sql = $builder->select('id', 'name', 'created_at')
            ->from('users')
            ->where('id=9')
            ->orwhere('id=10')
            ->where('name', '陈委平1')
            ->where('mobile', '=', 18106023801)
            ->where(function ($query) {
                    $query->where('id=11');
            })
            ->compile();

        var_dump($sql);
    }
    public function testWhereClosure() {
        $builder = new Builder();
        $sql = $builder->select('id', 'name', 'created_at')
            ->from('users')
            ->where(function ($query) {
                $query->where('id=11')->where('name', '1234')->orWhere('id', '=', 1234);
            })
            ->where('id=22')
            ->orWhere(function ($query) {
                $query->where('id=11')->where('name', '1234')->orWhere('id', '=', 1234);
            })
            ->compile();

        var_dump($sql);
    }

    public function testCreateSelect() {
        $builder = new Builder();
        $sql = $builder
            ->from('users')
            ->where(function ($query) {
                $query->where('id', '11')->where('name', 'name1')->orWhere('id', '=', 'name111');
            })
            ->where('id', '22')
            ->orWhere(function ($query) {
                $query->where('id=11')->where('name', 'name2')->orWhere('id', '=', 'name222');
            })
            ->select('id', 'name', 'created_at')
            ->compile();

        var_dump($sql);
        $this->assertIsArray($sql);
    }

    public function testWithoutSelect() {
        $builder = new Builder();
        $sql = $builder
            ->from('users')
            ->compile();

        var_dump($sql);
        $this->assertIsArray($sql);
    }



}
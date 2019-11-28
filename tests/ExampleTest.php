<?php



use PHPUnit\Framework\TestCase;
use ZiWen\SqlBuilder\Select;
use ZiWen\SqlBuilder\Where;
use ZiWen\SqlBuilder\Builder;

class ExampleTest extends  TestCase
{
    public function testHello() {
        echo "Hello World";
        $this->assertTrue(true);
    }

    public function testBuilder1() {
        $builder = new Builder();

        $sql = $builder->select('id', 'name', 'created_at')->from('users')->compile();
    }


}
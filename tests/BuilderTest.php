<?php declare(strict_types=1);


use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{

    public function testGet() {

        $builder = new \SqlBuilder\Builder();
        $sql = $builder->table('users')->where('id', 1)->get();

        var_dump($sql);
    }

}
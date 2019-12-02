<?php declare(strict_types=1);


use PHPUnit\Framework\TestCase;

use SqlBuilder\Builder;


class LaravelBuilderProtocolTest extends TestCase
{
    private $builder;
    public function setUp() : void
    {
        $this->builder = new Builder();

    }

    public function testHello() {
        echo 12345;
    }

    public function testSelect() {
        echo 1234;
        $users = $this->builder->table('users')->select('name', 'email as user_email')->get();

        var_dump($users);
    }

    public function testDistinct() {
        echo 1234;
        $users = $this->builder->table('users')->distinct()->get();

        var_dump($users);
    }

}
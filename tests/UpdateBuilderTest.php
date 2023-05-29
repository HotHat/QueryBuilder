<?php declare(strict_types=1);


use PHPUnit\Framework\TestCase;

class UpdateBuilderTest extends TestCase
{

    public function testUpdate() {

        $builder = new \QueryBuilder\Builder();
        $sql = $builder->table('users')->where('id', 1)->update([
            'name' => 'hello world',
            'id' => 1234

        ]);

        var_dump($sql);
    }

}
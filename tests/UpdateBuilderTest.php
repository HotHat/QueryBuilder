<?php declare(strict_types=1);


use PHPUnit\Framework\TestCase;

class UpdateBuilderTest extends TestCase
{

    public function testUpdate() {

        $update = new \SqlBuilder\UpdateBuilder();
        $sql = $update->table('users')->where('id', 1)->update([
            'name' => 'hello world',
            'id' => 1234

        ])->compile();

        var_dump($sql);
    }

}
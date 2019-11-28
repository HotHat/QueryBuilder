<?php declare(strict_types=1);




use PHPUnit\Framework\TestCase;
use ZiWen\SqlBuilder\scheme\Select;
use ZiWen\SqlBuilder\scheme\Where;
use ZiWen\SqlBuilder\scheme\OrWhere;
use ZiWen\SqlBuilder\scheme\WhereItem;

class SchemeTest extends TestCase
{

    public function testHello() {
        echo 1234;
        $this->assertTrue(true);
    }

    public function testSelect() {
        $s = new Select();

        $sql = $s->compile();

        $expect = 'SELECT *';

        $this->assertEquals($expect, $sql);

        $s->addItem('users.id');
        $s->addItem('users.name');

        $expect = 'SELECT `users`.`id`, `users`.`name`';

        $this->assertEquals($expect, $s->compile());

    }

    public function testFrom() {
        $f = new \ZiWen\SqlBuilder\scheme\From();

        $f->addItem('users');
        $f->addItem('orders');

        $expect = 'FROM `users`, `orders`';

        $this->assertEquals($expect, $f->compile());
    }

    public function testWhere() {
        $w1 = new Where('a=1');
        $w2 = new Where('a', 2);
        $w3 = new Where('a', '=', 3);

        $wc1 = $w1->compile();
        $wc2 = $w2->compile();
        $wc3 = $w3->compile();



        $this->assertEquals('a=1',   $wc1[0]);
        $this->assertEquals('`a`=\'?\'', $wc2[0]);
        $this->assertEquals('`a`=\'?\'', $wc3[0]);

    }

    public function testOrWhere() {
        $w1 = new OrWhere('a=1');
        $w2 = new OrWhere('a', 1);
        $w3 = new OrWhere('a', '=', 2);

        $wc1 = $w1->compile();
        $wc2 = $w2->compile();
        $wc3 = $w3->compile();

        $this->assertEquals('a=1',   $wc1[0]);
        $this->assertEquals('`a`=\'?\'', $wc2[0]);
        $this->assertEquals('`a`=\'?\'', $wc3[0]);
    }

    public function testWhereCondition() {
        $w1 = new Where('a=1');
        $w2 = new Where('a', 2);
        $w3 = new Where('a', '=', 3);
        $wo1 = new OrWhere('b=1');
        $wo2 = new OrWhere('b', 2);
        $wo3 = new OrWhere('b', '=', 3);

        $g = new \ZiWen\SqlBuilder\scheme\WhereCondition();
        $g->addWhere($w1);
        $g->addWhere($w2);
        $g->addWhere($w3);
        $g->addWhere($wo1);
        $g->addWhere($wo2);
        $g->addWhere($wo3);

        $sql = $g->compile();

        var_dump($sql);

        $this->assertIsArray($sql);

    }

    public function testWhereGroup()
    {

        $w1 = new Where('a=1');
        $w2 = new Where('a', 2);
        $w3 = new Where('a', '=', 3);
        $wo1 = new OrWhere('b=1');
        $wo2 = new OrWhere('b', 2);
        $wo3 = new OrWhere('b', '=', 3);

        $g = new \ZiWen\SqlBuilder\scheme\WhereGroup();
        $g->addWhere($w1);
        $g->addWhere($w2);
        $g->addWhere($w3);
        $g->addWhere($wo1);
        $g->addWhere($wo2);
        $g->addWhere($wo3);

        $sql = $g->compile();

        var_dump($sql);
        $this->assertIsArray($sql);
    }

    public function testOrWhereGroup()
    {

        $w1 = new Where('a=1');
        $w2 = new Where('a', 2);
        $w3 = new Where('a', '=', 3);
        $wo1 = new OrWhere('b=1');
        $wo2 = new OrWhere('b', 2);
        $wo3 = new OrWhere('b', '=', 3);

        $g = new \ZiWen\SqlBuilder\scheme\OrWhereGroup();
        $g->addWhere($w1);
        $g->addWhere($w2);
        $g->addWhere($w3);
        $g->addWhere($wo1);
        $g->addWhere($wo2);
        $g->addWhere($wo3);

        $sql = $g->compile();

        var_dump($sql);
        $this->assertIsArray($sql);
    }




    public function testLimit() {
        $l = new \ZiWen\SqlBuilder\scheme\Limit();
        $l->addItem('1');
        $expect = 'LIMIT 1';
        $this->assertEquals($expect, $l->compile());

        $l->addItem('3');
        $expect = 'LIMIT 1, 3';
        $this->assertEquals($expect, $l->compile());

    }

    public function testOrderBy()
    {
        $b = new \ZiWen\SqlBuilder\scheme\OrderBy();
        $b->addItem(['id', 'DESC']);
        $expect = 'ORDER BY `id` DESC';
        $this->assertEquals($expect, $b->compile());

        $b->addItem(['name', 'ASC']);
        $expect = 'ORDER BY `id` DESC, `name` ASC';
        $this->assertEquals($expect, $b->compile());
    }

    public function testGroupBy()
    {
        $g = new \ZiWen\SqlBuilder\scheme\GroupBy();
        $g->addItem('first_name');
        $g->addItem('status');

        $expect = 'GROUP BY `first_name`, `status`';
        $this->assertEquals($expect, $g->compile());
    }

}
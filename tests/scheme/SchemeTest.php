<?php declare(strict_types=1);




use PHPUnit\Framework\TestCase;
use QueryBuilder\Expr\Select;
use QueryBuilder\Expr\Where;
use QueryBuilder\Expr\OrWhere;
use QueryBuilder\Expr\WhereItem;
use QueryBuilder\Expr\Value;

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
        $f = new \ZiWen\QueryBuilder\scheme\From();

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

        $g = new \QueryBuilder\Expr\WhereCondition();
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

        $g = new \QueryBuilder\Expr\WhereGroup();
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

        $g = new \QueryBuilder\Expr\OrWhereGroup();
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
        $l = new \QueryBuilder\Expr\Limit();
        $l->addItem(\QueryBuilder\Expr\Value::raw('1'));
        $expect = 'LIMIT 1';
        $this->assertEquals($expect, $l->compile());

        $l->addItem(\QueryBuilder\Expr\Value::raw('3'));
        $expect = 'LIMIT 1, 3';
        $this->assertEquals($expect, $l->compile());

    }

    public function testOrderBy()
    {
        $b = new \ZiWen\QueryBuilder\scheme\OrderBy();
        $b->addItem(['id', 'DESC']);
        $expect = 'ORDER BY `id` DESC';
        $this->assertEquals($expect, $b->compile());

        $b->addItem(['name', 'ASC']);
        $expect = 'ORDER BY `id` DESC, `name` ASC';
        $this->assertEquals($expect, $b->compile());
    }

    public function testGroupBy()
    {
        $g = new \QueryBuilder\Expr\GroupBy();
        $g->addItem(Value::make('first_name'));
        $g->addItem(Value::raw('status'));

        $expect = 'GROUP BY `first_name`, `status`';
        $this->assertEquals($expect, $g->compile());
    }

    public function testUpdate() {
        $u = new \QueryBuilder\Expr\Update();
        $u->addItem(Value::make('users'));

        $sql = $u->compile();

        var_dump($sql);
    }

    public function testSet() {
        $u = new \QueryBuilder\Expr\UpdatePair();
        $u->addItem(Value::make(['name', 'name']));
        $u->addItem(Value::make(['id', 1]));

        $sql = $u->compile();

        var_dump($sql);
    }
    
    
    public function testJoin() {
        $join = new \QueryBuilder\Expr\Join('orders', 'user.id',  '=', 'orders.user_id');
        
        echo $join->compile();
        
        $expect = 'JOIN `orders` ON `user`.`id`=`orders`.`user_id`';
        $this->assertEquals($expect, $join->compile());
    }
    
    public function testLeftJoin() {
        $join = new \QueryBuilder\Expr\LeftJoin('orders', 'user.id', '=', 'orders.user_id');
    
        echo $join->compile();
    
        $expect = 'LEFT JOIN `orders` ON `user`.`id`=`orders`.`user_id`';
        $this->assertEquals($expect, $join->compile());
    }
    
    public function testRightJion() {
        $join = new \QueryBuilder\Expr\RightJoin('orders', 'user.id', '=', 'orders.user_id');
    
        echo $join->compile();
    
        $expect = 'RIGHT JOIN `orders` ON `user`.`id`=`orders`.`user_id`';
        $this->assertEquals($expect, $join->compile());
    }
    
    
    public function testTaleWithJoin() {
    
        $join = new \QueryBuilder\Expr\Join('orders', 'user.id', '=', 'orders.user_id');
        $leftJoin = new \QueryBuilder\Expr\LeftJoin('orders', 'user.id', '=', 'orders.user_id');
        $rightJoin = new \QueryBuilder\Expr\RightJoin('orders', 'user.id', '=', 'orders.user_id');
        
        
        $table = (new \QueryBuilder\Expr\Table())->asFrom();
        $table->addTable('users');
        
        $table->addJoin($join);
        $table->addJoin($leftJoin);
        $table->addJoin($rightJoin);
        
        
        $sql = $table->compile();
        
        echo $sql;
        
    }
}
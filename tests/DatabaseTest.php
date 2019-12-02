<?php declare(strict_types=1);



use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{

    /**
     * @var string
     */
    private $host;
    /**
     * @var int
     */
    private $port;
    /**
     * @var string
     */
    private $dbname;
    /**
     * @var string
     */
    private $passward;
    /**
     * @var string
     */
    private $user;
    /**
     * @var \SqlBuilder\Database
     */
    private $db;
    /**
     * @var \SqlBuilder\Builder
     */
    private $builder;


    protected function setUp(): void
    {
       $this->host = '192.168.68.8';
       $this->port = 3306;
       $this->dbname = 'ziwen';
       $this->user = 'homestead';
       $this->passward = 'secret';

      $this->db = new \SqlBuilder\Database($this->host, $this->port, $this->dbname, $this->user, $this->passward);
      $this->builder = new \SqlBuilder\Builder();

    }

    public function testQuery() {
        $sql = 'select * from user';

        $data = $this->db->select($sql, [], true);

        var_dump($data);

        var_dump($this->db->getRowCount());
    }

    public function testInsert() {
        $sql = "insert user (id, name, age) values (?, ?, ?)";

        $id = 5;
        [$sql, $bindValue] = $this->builder->table('user')->insert([
            'id' => $id,
            'name' => 'god',
            'age' => 30
        ]);

        $data = $this->db->insert($sql, $bindValue);

        var_dump($data);
        $this->assertEquals($id, $data);
    }

    public function testUpdate() {

        [$sql, $bindValue] = $this->builder->table('user')
            ->where('name', '=', 'god333')
            ->update([
            'age' => 35
        ]);

        var_dump($sql);
        var_dump($bindValue);
        // return;
        $data = $this->db->update($sql, $bindValue);
        // $data = $this->db->update('UPDATE `user`SET `age`=\'?\' WHERE `id`=\'?\'', [35, 1]);

        var_dump($data);
        $this->assertTrue($data);

    }

    public function testDelete() {
        [$sql, $bindValue] = $this->builder->table('user')
            ->where('id', 4)
            ->delete();

        var_dump($sql);
        var_dump($bindValue);
        // return;
        $data = $this->db->delete($sql, $bindValue);
        var_dump($data);

        $this->assertTrue($data);
    }

    public function testUnion() {
        $builder1 = new \SqlBuilder\Builder();
        $builder2 = new \SqlBuilder\Builder();
        $builder3 = new \SqlBuilder\Builder();

        $builder1->table('user')->where('id', 1);
        $builder3->table('user')->where('id', 4);

        $data = $builder2->table('user')->where('name', 'god')
            ->union($builder1)
            ->union($builder3)
            ->get();

        var_dump($data);
    }

}
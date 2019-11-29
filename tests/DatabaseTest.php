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


    protected function setUp(): void
    {
       $this->host = '192.168.1.135';
       $this->port = 3306;
       $this->dbname = 'ziwen_dev_rds';
       $this->user = 'qyt_dev';
       $this->passward = 'qyt_dev';

      $this->db = new \SqlBuilder\Database($this->host, $this->port, $this->dbname, $this->user, $this->passward);

    }

    public function testQuery() {
        $sql = 'select * from users';

        $data = $this->db->select($sql, [], true);

        var_dump($data);

        var_dump($this->db->getRowCount());
    }

}
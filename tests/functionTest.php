<?php

use PHPUnit\Framework\TestCase;
require_once dirname(__file__) . '/../backend/mysql.php';
require_once dirname(__file__) . '/../backend/functions.php';

class functionTest extends TestCase
{
    private static $db;
    public static function setUpBeforeClass() : void
    {
		self::$db = getDb();
    }
	public function test_get_current_semester_group_id()
	{
		$id_1 = get_current_semester_group_id(self::$db, "流动");
		$this->assertEquals($id_1, 1);
		$id_1 = get_current_semester_group_id(self::$db, "周一下午");
		$this->assertEquals($id_1, 2);
		$id_1 = get_current_semester_group_id(self::$db, "周二下午");
		$this->assertEquals($id_1, 3);
		$id_1 = get_current_semester_group_id(self::$db, "周三下午");
		$this->assertEquals($id_1, null);
	}
}
?>
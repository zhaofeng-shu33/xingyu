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
	public function test_get_current_semester_date()
	{
		$date_string_1 = get_current_semester_date(self::$db, 1);
		$this->assertEquals($date_string_1, '2018-09-03');
		$date_string_2 = get_current_semester_date(self::$db, 2);
		$this->assertEquals($date_string_2, '2019-03-04');
		$date_string_3 = get_current_semester_date(self::$db, 3);
		$this->assertEquals($date_string_3, '2019-09-07');
	}

	public function test_get_current_semester()
	{
		$semester_id = get_current_semester(self::$db);
		$this->assertEquals($semester_id, 3);
	}

	public function test_get_group_id()
	{
		$group_id = get_group_id(self::$db, "周二下午", 3);
		$this->assertEquals($group_id, 3);
		$group_id = get_group_id(self::$db, "流动", 3);
		$this->assertEquals($group_id, 1);
		$group_id = get_group_id(self::$db, "周二下午", 1);
		$this->assertEquals($group_id, 4);
		$group_id = get_group_id(self::$db, "周二下午", 2);
		$this->assertEquals($group_id, null);
	}
}
?>
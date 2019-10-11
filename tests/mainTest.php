<?php

use PHPUnit\Framework\TestCase;

class mainTest extends TestCase
{
    private static $root = 'http://localhost/xingyu/';
    public static function setUpBeforeClass() : void
    {
        if(getenv('XINGYU_ROOT')){
            self::$root = getenv('XINGYU_ROOT');
        }
    }
    public function test_add_delete_student_flow()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$root . 'add_student_flow.php');
        curl_setopt($ch, CURLOPT_POST, 1);   
        $payload = json_encode( array( 'student_name'=> '张六六', 'student_school'=>'hit', 'openid'=>'abc' ) );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        $server_output = curl_exec($ch);
        $json_out = json_decode($server_output);
        $this->assertEquals($json_out->err, 0);
        curl_setopt($ch, CURLOPT_URL, self::$root . 'delete_student_flow.php');
        $server_output = curl_exec($ch);
        $json_out = json_decode($server_output);
        $this->assertEquals($json_out->err, 0);
    }
	public function test_get_all_group()
	{
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$root . 'get_group_list.php?all=1');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
        $server_output = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$this->assertEquals($httpcode, 200);
        $json_out = json_decode($server_output);
        $this->assertEquals($json_out->err, 0);	
		$group_list = $json_out->result->group_list;
		$this->assertEquals(count($group_list), 4);
    }
    public function test_get_fixed_student()
    {
        $ch = curl_init();
        $group_name = '周一下午';
        curl_setopt($ch, CURLOPT_URL, self::$root . 'get_fixed_student.php?student_group=' . urlencode($group_name));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$this->assertEquals($httpcode, 200);
        $json_out = json_decode($server_output);
        $this->assertEquals($json_out->err, 0);	
        $student_list = $json_out->result->student_list;
        $this->assertEquals(count($student_list), 2);
    }
    public function test_get_student_list()
    {
        $ch = curl_init();
        $student_name_prefix = '张';
        curl_setopt($ch, CURLOPT_URL, self::$root . 'get_student_list.php?student_name_prefix=' . urlencode($student_name_prefix));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$this->assertEquals($httpcode, 200);
        $json_out = json_decode($server_output);
        $this->assertEquals($json_out->err, 0);	
        $student_list = $json_out->result->student_list;
        $this->assertEquals(count($student_list), 2);
    }
	public function test_add_activity()
	{
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$root . 'add_activity.php');
        curl_setopt($ch, CURLOPT_POST, 1);   
		$student_list = array('赵丰', '张三');		
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $payload = json_encode( array( 'week'=> 12, 'name'=>'周一下午', 'openid'=>'abc', 'student_list' =>  $student_list) );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $server_output = curl_exec($ch);
        $json_out = json_decode($server_output);
        $this->assertEquals($json_out->err, 0);

		$student_append_list = array('林粤');
        curl_setopt($ch, CURLOPT_URL, self::$root . 'append_activity.php');
        $payload = json_encode( array( 'week'=> 12, 'name'=>'周一下午', 'openid'=>'abc', 'student_list' =>  $student_append_list) );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $server_output = curl_exec($ch);
        $json_out = json_decode($server_output);
        $this->assertEquals($json_out->err, 0);	

        curl_setopt($ch, CURLOPT_URL, self::$root . 'remove_activity_student.php');
        $payload = json_encode( array( 'week'=> 12, 'name'=>'周一下午', 'openid'=>'abc', 'student_list' =>  array_merge($student_list, $student_append_list)) );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $server_output = curl_exec($ch);
        $json_out = json_decode($server_output);
        $this->assertEquals($json_out->err, 0);	
	}
	public function test_modify_student_group()
	{
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$root . 'modify_student_group.php?action=add');
        curl_setopt($ch, CURLOPT_POST, 1);   
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $payload = json_encode( array( 'group_name'=> '周二下午', 'student_name'=>'赵丰', 'openid'=>'abc') );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $server_output = curl_exec($ch);
        $json_out = json_decode($server_output);
        $this->assertEquals($json_out->err, 0);
        curl_setopt($ch, CURLOPT_URL, self::$root . 'modify_student_group.php?action=delete');
        $server_output = curl_exec($ch);
        $json_out = json_decode($server_output);
        $this->assertEquals($json_out->err, 0);	
	}
	public function test_plot_api()
	{
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::$root . 'plot.php?type=bar');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
        $this->assertFalse(curl_errno($ch));
        $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->assertEqual($status_code, 200);
	}
}

<?php

use PHPUnit\Framework\TestCase;

class mainTest extends TestCase
{
    private $root = 'http://localhost/xingyu/';
    public function test_add_student_flow()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->root . 'add_student_flow.php');
        curl_setopt($ch, CURLOPT_POST, 1);   
        $payload = json_encode( array( 'student_name'=> '张三', 'student_school'=>'hit', 'openid'=>'abc' ) );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $server_output = curl_exec($ch);
        $json_out = json_decode($server_output);
        $this->assertEquals($json_out->err, 0);
    }
	public function test_get_all_group()
	{
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->root . 'get_group_list.php?all=1');
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
        curl_setopt($ch, CURLOPT_URL, $this->root . 'get_fixed_student.php?student_group=' . urlencode($group_name));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $server_output = curl_exec($ch);
		$httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$this->assertEquals($httpcode, 200);
        $json_out = json_decode($server_output);
        $this->assertEquals($json_out->err, 0);	
        $student_list = $json_out->result->student_list;
        $this->assertEquals(count($student_list), 2);
    }
}
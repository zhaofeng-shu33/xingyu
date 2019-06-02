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
        $payload = json_encode( array( 'student_name'=> '张三', 'student_school'=>'hit' ) );
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        $server_output = curl_exec($ch);
        $json_out = json_decode($server_output);
        $this->assertEquals($json_out['err'], 0);
    }
}
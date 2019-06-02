<?php
date_default_timezone_set('Asia/Shanghai');

function connDB($dbConf)
{
	$conn = mysqli_connect($dbConf['host'], $dbConf['user'], $dbConf['pass']);

	if ($conn) {
		mysqli_query($conn, 'set names utf8;');
		return $conn;
	}
	return false;
}

function getDb()
{
	$db1 = array(
		'host' => 'localhost',
		'user' => 'xingyu',
		'pass' => 'xingyu',
		'DB_CHARSET'=> 'utf8mb4'
	);
	
	$db = connDB($db1);
	
	mysqli_select_db($db, 'xingyu');

	mysqli_query($db, 'set names utf8mb4');
	
	return $db;
}

function getTablePrefix(){
	return 'xingyu';
}

function getAppId(){
	return 'miniprogram appid';
}

function getAppSecret(){
	return 'miniprogram appsecret';
}

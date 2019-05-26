<?php
include_once 'mysql.php';
include_once 'functions.php';
if($_SERVER['REQUEST_METHOD'] == 'GET'){
    $code = $_GET['code'];
    if($code != null && $code != ''){
		$contents = file_get_contents('https://api.weixin.qq.com/sns/jscode2session?appid='.getAppId().'&secret='.getAppSecret().'&js_code='.$code.'&grant_type=authorization_code');
		$jsondecode = json_decode($contents);
		$openid = $jsondecode->openid;
		// session cache openid
		$session_key = $jsondecode->session_key;
		exitJson(0,'',array('openid'=>$openid, 'session_key'=>$session_key));
	}
	else{
		exitJson(1,'invalid code');
	}
}

$postdata = file_get_contents("php://input");
$jsondata = json_decode($postdata);

$openid = $jsondata->openid;
$nickname = $jsondata->nickname;
if($openid == null || $openid == ''){
    exitJson(2, 'null openid');
}
if($nickname == null || $nickname == ''){
    exitJson(3, 'null nickname');
}

	//save the openid to the database, where nickname matches database nickname
	$db = getDb();
	$sql = 'select id from ' . getTablePrefix() . "_student where wechat_nickname = '$nickname'";	
	$res = mysqli_query($db, $sql) or die(mysqli_error($db));
	$row = mysqli_fetch_assoc($res);
	if($row['id'] == null){
		exitJson(5, 'student not exists');
	}
	$id = $row['id'];
	$sql_u = 'update '. getTablePrefix() . "_student set wechat_openid = '$openid' where id = $id";
	mysqli_query($db, $sql_u) or die(mysqli_error($db));
	exitJson(0, 'succ');
?>

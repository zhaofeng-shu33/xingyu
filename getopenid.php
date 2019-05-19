<?php
include_once 'mysql.php';
include_once 'functions.php';

$postdata=file_get_contents("php://input");

$jsondata=json_decode($postdata);

$code=$jsondata->code;

if($code!=''){
	$contents = file_get_contents('https://api.weixin.qq.com/sns/jscode2session?appid='.getAppId().'&secret='.getAppSecret().'&js_code='.$code.'&grant_type=authorization_code');
	$jsondecode = json_decode($contents);
    $openid = $jsondecode->openid;
    $session_key=$jsondecode->session_key;
    exitJson(0,'',array('openid'=>$openid,'session_key'=>$session_key));
}else{
	exitJson(1,'invalid code');
}

?>
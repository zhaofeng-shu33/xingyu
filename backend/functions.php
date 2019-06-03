<?php
include_once 'mysql.php';
function exitJson($err, $msg , $result='')
{
	echo json_encode(array('err'=>$err, 'msg'=>$msg , 'result'=>$result));
	exit();
}

//! check the user with $openid is admin
function is_admin($db, $openid)
{
	$sql_p = 'select id from '.getTablePrefix()."_student where wechat_openid = '$openid'";
    $res_p = mysqli_query($db, $sql_p);
    $row_p = mysqli_fetch_assoc($res_p);
    return ($row_p['id'] != null);
}

function get_current_semester($db){
	return 2;
}

function get_semester_start_time($db, $semester){
    $sql_get_start_time = 'select start_time from '.getTablePrefix()."_semester where id = $semester";
	$res = mysqli_query($db, $sql_get_start_time) or die(mysqli_error($db));
	$row = mysqli_fetch_assoc($res);

    return $row['start_time'];
}
?>

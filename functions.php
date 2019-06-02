<?php

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
?>

<?php

function exitJson($err, $msg , $result='')
{
	echo json_encode(array('err'=>$err, 'msg'=>$msg , 'result'=>$result));
	exit();
}

?>

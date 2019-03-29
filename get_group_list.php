<?php
include_once 'mysql.php';
include_once 'functions.php';
$db = getDb();
$sql = 'select name from '.getTablePrefix()."_group where semester_id = 2";
$res=mysqli_query($db, $sql) or die(mysqli_error($db));
$row = mysqli_fetch_all($res);
exitJson(0, '',array('group_list'=>$row));

?>
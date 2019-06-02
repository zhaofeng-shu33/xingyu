<?php
include_once 'mysql.php';
include_once 'functions.php';
$db = getDb();
$sql = 'select name, location, time from '.getTablePrefix()."_activity where special = 1";
$res=mysqli_query($db, $sql) or die(mysqli_error($db));
$row = mysqli_fetch_all($res);
exitJson(0, '',array('special_activity_list'=>$row));

?>
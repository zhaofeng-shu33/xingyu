<?php
include_once 'mysql.php';
include_once 'functions.php';
$name_prefix = $_GET['student_name_prefix'];
if($name_prefix == null || $name_prefix == ''){
    exitJson(1, 'null name prefix');
}
$db = getDb();
$sql = 'select name, school from '.getTablePrefix()."_student where group_id = 1 and name like '$$name_prefix'";
$res=mysqli_query($db, $sql) or die(mysqli_error($db));
$row = mysqli_fetch_assoc($res);
exitJson(0, '',array('student_list'=>$row));

?>
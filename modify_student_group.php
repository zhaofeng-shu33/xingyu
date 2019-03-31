<?php
include_once 'mysql.php';
include_once 'functions.php';
$postdata=file_get_contents("php://input");

$jsondata=json_decode($postdata);

$group_id=$jsondata->group_id;
$name=$jsondata->student_name;
if($name == null || $name == ''){
    exitJson(1, 'null name');
}
if($group_id == null || gettype($group_id) != 'integer' || $group_id <= 0){
    exitJson(2, 'invalid group id');
}
$db = getDb();
$sql_s = 'select id from '.getTablePrefix()."_student where name = '$name'";
$res=mysqli_query($db, $sql_s) or die(mysqli_error($db));
$row = mysqli_fetch_assoc($res);
if($row['id'] == null){
    exitJson(3, 'student not exists');
}
$student_id = $row['id'];
$sql = 'update '.getTablePrefix()."_student_group set group_id = $group_id where student_id = $student_id";
$res=mysqli_query($db, $sql) or die(mysqli_error($db));
exitJson(0, '');
?>
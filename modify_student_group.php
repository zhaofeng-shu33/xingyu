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
$action=$_GET['action'];
if($action != 'add' && $action != 'delete'){
    exitJson(4, 'invalid action');
}
$db = getDb();
$sql_s = 'select id from '.getTablePrefix()."_student where name = '$name'";
$res=mysqli_query($db, $sql_s) or die(mysqli_error($db));
$row = mysqli_fetch_assoc($res);
if($row['id'] == null){
    exitJson(3, 'student not exists');
}
$student_id = $row['id'];
// for delete action, first check if the student belongs to the specified group exists
$sql = 'select id frin '.getTablePrefix()."_student_group where group_id = $group_id and student_id = $student_id";
$res = mysqli_query($db, $sql) or die(mysqli_error($db));
if($action =='delete' && $row['id'] == null){
    exitJson(5, 'student does not belong to the group.');
}
// for add action, if exists, return directly
else if($action == 'add' && $row['id'] != null){
    exitJson(0, '');
}
if($action == 'delete'){
    $sql_a = 'delete from '.getTablePrefix()."_student_group where group_id = $group_id and student_id = $student_id";
}
else{ // add
    $sql_a = 'insert into '.getTablePrefix()."_student_group (group_id, student_id) values ($group_id, $student_id)";
}
mysqli_query($db, $sql_a) or die(mysqli_error($db));
exitJson(0, '');
?>
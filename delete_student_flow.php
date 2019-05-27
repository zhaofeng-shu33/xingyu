<?php
include_once 'mysql.php';
include_once 'functions.php';
$postdata=file_get_contents("php://input");

$jsondata=json_decode($postdata);

$school=$jsondata->student_school;
$name=$jsondata->student_name;
$openid = $jsondata->openid;
if($openid != null && $openid != ''){
    $not_admin = !is_admin($db, $openid);
}
else{
    $not_admin = True;
}
if($not_admin){
    exitJson(44, 'you do not have the privilege');
}
if($name == null || $name == ''){
    exitJson(1, 'null name');
}
if($school == null || $school == ''){
    exitJson(2, 'null school');
}
$db = getDb();
$sql_s = 'select id from '.getTablePrefix()."_student where name = '$name' and school = '$school'";
$res=mysqli_query($db, $sql_s) or die(mysqli_error($db));
$row = mysqli_fetch_assoc($res);
if($row['id'] == null){
    exitJson(3, 'student not exists');
}
// check activity
$student_id = $row['id'];
$sql_a = 'select count(activity_id) from '.getTablePrefix()."_student_activity where student_id = $student_id";
$res_a = mysqli_query($db, $sql_a) or die(mysqli_error($db));
$row = mysqli_fetch_assoc($res_a);
if($row['count(activity_id)'] > 0){
    exitJson(4, 'student has activity');
}
// delete the student from flow group;
//! Todo: check the student is really flow.
$sql = 'delete from '.getTablePrefix()."_student where id = $student_id";
$res=mysqli_query($db, $sql) or die(mysqli_error($db));
exitJson(0, '');
?>
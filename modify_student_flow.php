<?php
include_once 'mysql.php';
include_once 'functions.php';
$postdata=file_get_contents("php://input");

$jsondata=json_decode($postdata);

$school=$jsondata->student_school;
$name=$jsondata->student_name;
$openid = $jsondata->openid;

if($openid != null){
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
$sql_s = 'select id from '.getTablePrefix()."_student where name = '$name'";
$res=mysqli_query($db, $sql_s) or die(mysqli_error($db));
$row = mysqli_fetch_assoc($res);
if($row['id'] == null){
    exitJson(3, 'student not exists');
}
$sql = 'update '.getTablePrefix()."_student set school = '$school' where name = '$name'";
$res=mysqli_query($db, $sql) or die(mysqli_error($db));
exitJson(0, '');
?>
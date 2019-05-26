<?php
include_once 'mysql.php';
include_once 'functions.php';
$postdata=file_get_contents("php://input");

$jsondata=json_decode($postdata);

$school=$jsondata->student_school;
$name=$jsondata->student_name;
$openid = $jsondata->openid;

if($name == null || $name == ''){
    exitJson(1, 'null name');
}
if($school == null || $school == ''){
    exitJson(2, 'null school');
}
$db = getDb();
if($openid != null){
    $is_admin_result = is_admin($db, $openid);
}
else{
    $is_admin_result = False;
}
$sql_s = 'select id from '.getTablePrefix()."_student where name = '$name'";
$res=mysqli_query($db, $sql_s) or die(mysqli_error($db));
$row = mysqli_fetch_assoc($res);
if($row['id'] == null){ // student not exist
    $sql = 'insert into '.getTablePrefix()."_student (name, school) values ('$name', '$school')";
    $res=mysqli_query($db, $sql) or die(mysqli_error($db));    
    $student_id =  mysqli_insert_id($db);
}
else{// if student exists, don't trigger any error
    $student_id = $row['id'];
    $sql_sg = 'select id from '.getTablePrefix()."_student_group where student_id = $student_id";
    $res_sg=mysqli_query($db, $sql_sg) or die(mysqli_error($db));    
    $row_sg = mysqli_fetch_assoc($res_sg);
    if($row_sg['id'] != null){
        exitJson(3, 'student already has a group');
    }
}
$sql_ig = 'insert into '.getTablePrefix()."_student_group (group_id, student_id) values (1, $student_id)";
$res_ig=mysqli_query($db, $sql_ig) or die(mysqli_error($db));    

exitJson(0, '',array('student_id'=>$student_id, 'is_admin'=>$is_admin_result));
?>
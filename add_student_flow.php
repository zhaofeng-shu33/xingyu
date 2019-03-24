<?php
include_once 'mysql.php';
include_once 'functions.php';
$postdata=file_get_contents("php://input");

$jsondata=json_decode($postdata);

$school=$jsondata->student_school;
$name=$jsondata->student_name;
if($name == null || $name == ''){
    exitJson(1, 'null name');
}
if($school == null || $school == ''){
    exitJson(2, 'null school');
}
$db = getDb();
$sql = 'insert into '.getTablePrefix()."_student (name, school) values ('$name', '$school')";
$res=mysqli_query($db, $sql) or die(mysqli_error($db));
$student_id = mysqli_insert_id($db);
exitJson(0, '',array('student_id'=>$student_id));
?>
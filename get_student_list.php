<?php
include_once 'mysql.php';
include_once 'functions.php';
$name_prefix = $_GET['student_name_prefix'];
if($name_prefix == null || $name_prefix == ''){
    exitJson(1, 'null name prefix');
}
$semester_id = $_GET['semester'];
if($semester_id == null){
    $semester_id = 2;
}
if(intval($semester_id) == 0){
    exitJson(1, 'invalid semester');
}
else{
    $semester_id = intval($semester_id);
}

$sql_s = 'select id from '.getTablePrefix()."_group where name = '流动' and semester_id = $semester_id";
$res_s = mysqli_query($sql_s) or die(mysqli_error($db));
$row_s = mysqli_fetch_assoc($res_s);
$flow_group_id = $row_s['id'];

$db = getDb();
$sql = 'select s.name, s.school from '.getTablePrefix().'_student as s, '.getTablePrefix()."_student_group as sg where sg.group_id = $flow_group_id and sg.student_id = s.id and s.name like '$name_prefix%'";
$res=mysqli_query($db, $sql) or die(mysqli_error($db));
$row = mysqli_fetch_all($res);
exitJson(0, '',array('student_list'=>$row));

?>
<?php
include_once 'mysql.php';
include_once 'functions.php';
$name_prefix = $_GET['student_name_prefix'];
if($name_prefix == null || $name_prefix == ''){
    exitJson(1, 'null name prefix');
}

$db = getDb();
$sql = 'select s.name, s.school from '.getTablePrefix().'_student as s, '.getTablePrefix()."_semester_group as sg where sg.group_id = 1 and sg.student_id = s.id and s.name like '$name_prefix%'";
$res=mysqli_query($db, $sql) or die(mysqli_error($db));
$row = mysqli_fetch_all($res);
exitJson(0, '',array('student_list'=>$row));

?>
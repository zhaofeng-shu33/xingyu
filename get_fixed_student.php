<?php
include_once 'mysql.php';
include_once 'functions.php';
$group = $_GET['student_group'];
$semester_id = $_GET['semester'];
if($group == null || $group == ''){
    exitJson(1, 'null group');
}
if($group == '流动'){
    exitJson(0, 'result for flow is not allowed', array('student_list'=>[]));
}
if($semester_id == null){
    $semester_id = 2;
}
elseif(intval($semester_id) == 0){
    exitJson(2, 'invalid semester');
}
else{
    $semester_id = intval($semester_id);
}
$db = getDb();
$sql = 'select s.name, s.school from '.getTablePrefix().'_student as s, '.getTablePrefix().'_group as g, '.getTablePrefix().  "_semester_group as sg where g.name = '$group' and g.id = sg.group_id and sg.student_id = s.id and g.semester_id = $semester_id";
$res=mysqli_query($db, $sql) or die(mysqli_error($db));
$row = mysqli_fetch_all($res);
exitJson(0, '',array('student_list'=>$row));

?>
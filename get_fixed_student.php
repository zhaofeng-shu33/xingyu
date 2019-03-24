<?php
include_once 'mysql.php';
include_once 'functions.php';
$group = $_GET['student_group'];
if($group == null || $group == ''){
    exitJson(1, 'null group');
}
if($group == '流动')
    exitJson(0, 'result for flow is not allowed', array('student_list'=>[]));

$db = getDb();
$sql = 'select s.name, s.school from '.getTablePrefix().'_student as s, '.getTablePrefix()."_group as g where g.name = '$group' and g.id = s.group_id";
$res=mysqli_query($db, $sql) or die(mysqli_error($db));
$row = mysqli_fetch_all($res);
exitJson(0, '',array('student_list'=>$row));

?>
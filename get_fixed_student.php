<?php
include_once 'mysql.php';
include_once 'functions.php';
$group = $_GET['student_group'];
if($group == null || $group == ''){
    exitJson(1, 'null group');
}
$db = getDb();
$sql = 'select s.name from '.getTablePrefix().'_student as s, '.getTablePrefix()."_group as g where g.name = '$group' and g.id = s.group_id";
$res=mysqli_query($db, $sql) or die(mysqli_error($db));
$row = mysqli_fetch_assoc($res);
exitJson(0, '',array('student_list'=>$row));

?>
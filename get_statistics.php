<?php
include_once 'mysql.php';
include_once 'functions.php';
$db = getDb();
$data = [array('school'=>'hit'), array('school'=>'sust'), array('school'=>'thu'), array('school'=>'pku'), array('school'=>'szu')];
foreach($data as &$info){
    $school = $info['school'];
    $sql_s = 'select count(s.id) as c from '.getTablePrefix().'_student as s, '.getTablePrefix()."_student_activity as sa where s.id = sa.student_id and s.school = '$school'";
    $res_s=mysqli_query($db, $sql_s) or die(mysqli_error($db));
    $row = mysqli_fetch_assoc($res_s);
    $info['total_count'] = $row['c'];
    $sql = 'select count(distinct s.id) as c from '.getTablePrefix().'_student as s, '.getTablePrefix()."_student_activity as sa where s.id = sa.student_id and s.school = '$school'";
    $res=mysqli_query($db, $sql) or die(mysqli_error($db));
    $row = mysqli_fetch_assoc($res);
    $info['total_student'] = $row['c'];
}
exitJson(0, '', $data);
?>
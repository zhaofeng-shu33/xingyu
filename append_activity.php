<?php
include_once 'mysql.php';
include_once 'functions.php';
$postdata=file_get_contents("php://input");

$jsondata=json_decode($postdata);

$week=$jsondata->week;
$name=$jsondata->name;
$list=$jsondata->student_list;
if($week == null || gettype($week)!='integer'){
    if($week == null)
        exitJson(1, 'null week');
    else
        exitJson(1, 'invalid week, type '.gettype($week));
}
if($name == null || $name == ''){
    exitJson(2, 'null name');
}
if($list == null || gettype($list)!='array'){
    exitJson(3, 'invalid student_list');
}
$db = getDb();
// create the activity
if(strpos($name, '��ɫ') == FALSE && $name != '�ܶ�����'){
    $location = 'ͯ��ʱ��';
}
else{
    $location = '��ɫ�껪';
}
// convert szu calendar to ����
// 2019-3-4 ~ week 1
$date=date_create("2019-3-4");
$interval_int = 7 * ($week - 1);
date_add($date, date_interval_create_from_date_string($interval_int." days"));
$date_str = date_format($date, 'Y-m-d');
// first check whether the activity exists
$sql_check = 'select id from '.getTablePrefix()."_activity where name = '$name' and location = '$location' and time = '$date_str'";
$res = mysqli_query($db, $sql_check) or die(mysqli_error($db));
$row = mysqli_fetch_assoc($res);
if($row['id'] == null){
    exitJson(5, 'activity not exists');
}
$activity_id = $row['id'];
foreach($list as $student){
    // first get student id
    $sql_s = 'select id from '.getTablePrefix()."_student where name = '$student'";
    $res_s = mysqli_query($db, $sql_s) or die(mysqli_error($db));
    $row_s = mysqli_fetch_assoc($res_s);
    if($row_s['id'] == null){
        exitJson(4, 'student '. $student. ' not exists in db');
    }    
    $student_id = $row_s['id'];
    $sql_s_a = 'select id from '.getTablePrefix()."_student_activity where student_id = $student_id and activity_id = $activity_id";
    $res_s_a = mysqli_query($db, $sql_s_a) or die(mysqli_error($db));
    $row_s_a = mysqli_fetch_assoc($res_s_a);
    if($row_s_a['id'] != null){ // student has already joined this activity
        continue;
    }    
    $sql_r = 'insert into '.getTablePrefix()."_student_activity (student_id, activity_id) values ('$student_id', '$activity_id')";
    $res_r=mysqli_query($db, $sql_r) or die(mysqli_error($db));
}
exitJson(0, '');
?>
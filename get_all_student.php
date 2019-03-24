<?php
include_once 'mysql.php';
include_once 'functions.php';
$name = $_GET['student_group'];
$week = $_GET['week'];

if($name == null || $name == ''){
    exitJson(1, 'null group');
}
if($week == null || intval($week) == 0){
    exitJson(2, 'invalid week');
}
if($name == '流动')
    exitJson(0, 'no result for flow',[]);
if(strpos($name, '金色') == FALSE && $name != '周二下午'){
    $location = '童伴时光';
}
else{
    $location = '金色年华';
}
$db = getDb();

// convert szu calendar to 阳历
// 2019-3-4 ~ week 1
$date=date_create("2019-3-4");
$interval_int = 7 * (intval($week) - 1);
date_add($date, date_interval_create_from_date_string($interval_int." days"));
$date_str = date_format($date, 'Y-m-d');
$sql = 'select id from '.getTablePrefix()."_activity where location = '$location' and time = '$date_str'";
$res = mysqli_query($db, $sql) or die(mysqli_error($db));
$row = mysqli_fetch_assoc($res);
if($row['id'] == null){
    exitJson(0, 'activity not exist',[]);
}
$activity_id = $row['id'];
$sql_s = 'select s.name, s.school from '.getTablePrefix().'_student as s, '.getTablePrefix()."_student_activity as sa where s.id = sa.student_id and sa.activity_id = '$activity_id'";
$res_s=mysqli_query($db, $sql_s) or die(mysqli_error($db));
$row_s = mysqli_fetch_all($res_s);
exitJson(0, '', array('student_list'=>$row_s));
?>